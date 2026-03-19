<?php

namespace Lovillela\BlogApp\Services;
use HTMLPurifier;
use HTMLPurifier_Config;
/**
 * 
 */
final class InputSanitizationService{

  private HTMLPurifier $htmlPurifier;
  private HTMLPurifier_Config $htmlPurifierConfig;
  private const CACHE_PATH = __DIR__ . '/../../cache/htmlpurifier/';
  private const ALLOWED_TAGS = 'a[href|style],b,blockquote,br,code,div[style],' .
                             'h1,h2,h3,h4,h5,h6,hr,i,img[src],li,ol,p,pre,strong,' .
                             'table,tbody,td,th,thead,tr,ul';

  public function __construct() {
    $this->createCacheDirectory();
    $this->htmlPurifierConfig = HTMLPurifier_Config::createDefault();
    $this->htmlPurifierConfig->set('Cache.SerializerPath', $this::CACHE_PATH);
    $this->htmlPurifierConfig->set('HTML.Allowed', $this::ALLOWED_TAGS);
    $this->htmlPurifierConfig->set('Core.EscapeInvalidTags', true);
    $this->htmlPurifier = new HTMLPurifier($this->htmlPurifierConfig);
  }
  public function urlRouteSanitize(string $url): string{
    return preg_replace('/[^a-zA-Z0-9\-\+\/\?\=\&\_\.\%]/', '', $url);
  }

  public function urlInputSanitize(string $url): string{
    return preg_replace('/[^a-zA-Z0-9\-]/', '', $url);
  }
  
  public function postContentSanitize(string $content): string{
    return $this->htmlPurifier->purify($content);
  }

  public function postTitleSanitize(string $title): string{
    return trim(strip_tags($title));
  }

  public function slugSanitize(string $slug): string{
    return preg_replace('/[^a-zA-Z0-9\-]/', '', $slug);
  }

  public function idSanitize(int $id): int {
    return (int)preg_replace('/[^0-9]/', '', $id);
  }

  public function usernameSanitize(string $username): string {
    return (string)preg_replace('/[^a-zA-Z0-9\.\_]/', '', $username);
  }

  public function emailSanitize(string $email): string {
    return filter_var($email, FILTER_SANITIZE_EMAIL);
  }

  public function displayPostSanitize(array $post): array {
    
    $post['title'] = htmlspecialchars($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    /**
     * É possível utilizar, mas é uma biblioteca pesada.
     * This may be used, it is a heavy library however.
     */
    $post['content'] = $this->postContentSanitize($post['content']);

    return $post;
  }

  private function createCacheDirectory() {
    if (!is_dir($this::CACHE_PATH)) {
      mkdir($this::CACHE_PATH, 755, true);
    }
  }
}