<?php

namespace Lovillela\BlogApp\Config\Views;

enum ViewPath: string{
  case BASE_PATH = __DIR__ . '/../../src/Views/';
  case BASE_VIEW = BASE_PATH . 'BaseView.php';
}
