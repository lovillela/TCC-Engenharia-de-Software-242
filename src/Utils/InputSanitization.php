<?php 

namespace Lovillela\BlogApp\Utils;

final class InputSanitization{

  public static function urlRouteSanitize(string $url){
    return preg_replace('/[^a-zA-Z0-9\-\/\?\=\&]/', '', $url);
  }

  public static function urlInputSanitize(string $url){
    return preg_replace('/[^a-zA-Z0-9\-]/', '', $url);
  }
  
  public static function postContentSanitize(string $content){

    $allowedTags = ['p', 'a', 'b', 'br', 'code', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 
    'img', 'li', 'ol', 'q', 's', 'section', 'strong', 'sub', 'sytle', 'table', 'tbody', 
    'td', 'th', 'thead', 'ul'];

    return strip_tags($content, $allowedTags);
  }

  public static function postTitleSanitize(string $title){
    return strip_tags($title);
  }
}
