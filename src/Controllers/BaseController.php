<?php 

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Config\Views\ViewPath;
use Lovillela\BlogApp\Models\Views\ViewData;

/**
 * Base class for the common prepareView function to all controllers
 * Classe base para a função comum prepareView para todos os controllers
 */
abstract class BaseController{
  protected function prepareView(ViewPath $view, string $headTitle, array $bodyData) {
    return new ViewData($view->getPath(), $headTitle, $bodyData);
  }
}