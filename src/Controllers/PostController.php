<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Models\Users\UserIdentity;

final class PostController {

  private PostManagementService $postService;
  private RedirectService $redirectService;
  private AuthManagerService $authManagerService;
  private $messages = array();
  private $posts = array();
  private $data = array();
  private array $dependencyContainer;

  public function __construct(array $dependencyContainer) {
    $this->dependencyContainer = $dependencyContainer;
    $this->postService = $this->dependencyContainer['PostManagementService'];
    $this->redirectService = $this->dependencyContainer['RedirectService'];
    $this->authManagerService = $this->dependencyContainer['AuthManagerService'];
  }

  public function index() {
    $this->messages = [
      'title' => 'Post Home',
      'headerText' => 'Post Home',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $this->posts = $this->getAllPosts();

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostHomeView.php');
    $render->render($this->messages, $this->posts);
  }

  public function show($slug) {

    $this->data = $this->getPostBySlug($slug);
    
    $this->messages = [
      'title' => $this->data['title'],
      'headerText' => $this->data['title'],
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostView.php');
    $render->render($this->messages, $this->data);
  }

  public function redirectToTrailingSlash($slug) {
    $this->redirectService->redirectToTrailingSlash();
  }

  public function addPostAction() {

    if (!$this->authManagerService->isSessionActive()) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
    }

    $userData = $this->authManagerService->getUserData();

    if (!isset($userData) || !$this->authManagerService->isPostCreationAllowed($userData)) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
    }

    $title = $_POST['postTitle'];
    $text = $_POST['blogPost'];
    
    $response = $this->postService->create($title, $text, $userData->userId);

     $this->messages = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => $response['Message'],
      'generalMessage' => '',
    ];

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostFormView.php');
    $render->render($this->messages);
  }

  public function addPostForm() {
    $this->regularUserCheck();

    $this->messages = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostFormView.php');
    $render->render($this->messages);
  }

  private function getPostBySlug(string $slug){
    $getPost = $this->postService;
    return $getPost->getPostBySlug($slug);
  }
  private function getAllPosts(){
    $getAllPosts = $this->postService;
    return $getAllPosts->getAllPosts();
  }

}