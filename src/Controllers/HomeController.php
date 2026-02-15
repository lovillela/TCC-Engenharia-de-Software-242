<?php
namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Models\Views\ViewData;
use Lovillela\BlogApp\Config\Views\ViewPath;

final class HomeController{

  private ViewData $viewData;
  private array $dependencyContainer;
  private ViewRenderService $viewRenderService;

  public function __construct(array $dependencyContainer) {
  $this->dependencyContainer = $dependencyContainer;
  $this->viewRenderService = $this->dependencyContainer['ViewRenderService'];
  }
  public function index(){
    
    $headTitle = 'Blog App - Home Page';

    $bodyData=[
      'title' => 'Blog App - Home Page',
      'allPostsLink' => 'All Posts',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $this->viewData = new ViewData(ViewPath::FRONTEND_HOMEPAGE->getPath(), $headTitle, $bodyData);
    
    $this->viewRenderService->render($this->viewData);
  }
}