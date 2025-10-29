<?php

namespace Lovillela\BlogApp\Services;

use Exception;

class ViewRenderService{

  private $viewFile;

  public function __construct(string $requestedView){
    $this->viewFile = $requestedView;
  }

  public function render(array $messages, ?array $data = NULL){

    extract($messages);

    if (isset($data)) {
      extract($data);
    }
    
    if (!(file_exists($this->viewFile))) {
      throw new Exception((string)$e . "\nView file not found", 1);
    }

    return (include $this->viewFile);
  }
}