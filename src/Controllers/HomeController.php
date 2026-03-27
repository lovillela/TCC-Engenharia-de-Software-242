<?php
namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Config\Views\ViewPath;

final class HomeController extends BaseController{

  private ViewRenderService $viewRenderService;

  public function __construct(array $dependencyContainer) {
    parent::__construct($dependencyContainer);
    $this->viewRenderService = $dependencyContainer['ViewRenderService'];
  }
  public function index(){
    
    $headTitle = 'Blog App - Home Page';

    $bodyData=[
      'title' => 'Blog App - Home Page',
      'allPostsLink' => 'All Posts',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_HOMEPAGE, $headTitle, $bodyData);
    
    $this->viewRenderService->render($viewData);
  }
}