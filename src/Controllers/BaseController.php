<?php 

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Config\Views\ViewPath;
use Lovillela\BlogApp\Models\Views\ViewData;
use Lovillela\BlogApp\Services\AuthManagerService;

/**
 * Base class for the common prepareView function to all controllers
 * Classe base para a função comum prepareView para todos os controllers
 */
abstract class BaseController{

  protected AuthManagerService $authManagerService;

  public function __construct(array $dependencyContainer){
    $this->authManagerService = $dependencyContainer['AuthManagerService'];
  }

  protected function prepareView(ViewPath $view, string $headTitle, array $bodyData) {

    $bodyData['isLoggedIn'] = $this->authManagerService->isSessionActive();
    
    return new ViewData($view->getPath(), $headTitle, $bodyData);
  }
}