<?php

namespace Lovillela\BlogApp\Models\Views;

class ViewData {

  public readonly string $viewFilePath;
  public readonly string $headTitle;
  public readonly array $bodyData;

  public function __construct(string $viewFilePath, string $headTitle, array $bodyData = []){
      $this->viewFilePath = $viewFilePath;
      $this->headTitle = $headTitle;
      $this->bodyData = $bodyData;
  }

}