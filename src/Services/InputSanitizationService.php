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
  private const ALLOWED_TAGS = ['p,b,a[href],i,br,ul,ol,li,img[src],h1,h2,h3,h4,h5,h6,blockquote,code'];

  public function __construct() {
    $this->createCacheDirectory();
    $this->htmlPurifierConfig = HTMLPurifier_Config::createDefault();
    $this->htmlPurifierConfig->set('Cache.SerializerPath', $this::CACHE_PATH);
    $this->htmlPurifierConfig->set('HTTP.Allowed', $this::ALLOWED_TAGS);
    $this->htmlPurifier = new HTMLPurifier($this->htmlPurifierConfig);
  }
  public function urlRouteSanitize(string $url){
    return preg_replace('/[^a-zA-Z0-9\-\/\?\=\&\_\.\%]/', '', $url);
  }

  public function urlInputSanitize(string $url){
    return preg_replace('/[^a-zA-Z0-9\-]/', '', $url);
  }
  
  public function postContentSanitize(string $content): string{
    return $this->htmlPurifier->purify($content);
  }

  public function postTitleSanitize(string $title){
    return trim(strip_tags($title));
  }

  private function createCacheDirectory() {
    if (!dir($this::CACHE_PATH)) {
      mkdir($this::CACHE_PATH, 755);
    }
  }
}