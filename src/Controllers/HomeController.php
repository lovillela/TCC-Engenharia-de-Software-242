<?php
namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\ViewRenderService;

final class HomeController{

    private array $messages;

    private $viewRenderer;

    public function __construct() {
      
    }
    public function index(){
      
      $this->viewRenderer = new ViewRenderService(__DIR__ . '/../Views/Frontend/HomePageView.php');

      $this->messages=[
      'title' => 'Blog App - Home Page',
      'headerText' => 'Home Page',
      'errorMessage' => '',
      'generalMessage' => '',
      ];

      $this->viewRenderer->render($this->messages);
    }
}