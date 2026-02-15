<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Models\Users\UserIdentity;
use Lovillela\BlogApp\Models\Views\ViewData;
use Lovillela\BlogApp\Config\Views\ViewPath;

final class PostController extends BaseController{

  private PostManagementService $postService;
  private RedirectService $redirectService;
  private AuthManagerService $authManagerService;
  private ViewRenderService $viewRenderService;
  private ViewData $viewData;
  private array $dependencyContainer;

  public function __construct(array $dependencyContainer) {
    $this->dependencyContainer = $dependencyContainer;
    $this->postService = $this->dependencyContainer['PostManagementService'];
    $this->redirectService = $this->dependencyContainer['RedirectService'];
    $this->authManagerService = $this->dependencyContainer['AuthManagerService'];
    $this->viewRenderService = $this->dependencyContainer['ViewRenderService'];
  }

  public function index() {

    $headTitle = 'All Posts';

    $posts = $this->getAllPosts();

    $bodyData = [
      'title' => 'Post Home',
      'headerText' => 'Post Home',
      'errorMessage' => '',
      'generalMessage' => '',
      'posts' => $posts,
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POST_HOME, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function show($slug) {

    $post = $this->getPostBySlug($slug);

    $headTitle = $post['title'];
    
    $bodyData = [
      'title' => $post['title'],
      'content' => $post['content'],
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POST, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
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

    if (!isset($userData) || !$this->authManagerService->canCreatePost($userData)) {
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

  public function deletePostAction(int $postId) {

    if (!$this->authManagerService->isSessionActive()) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
    }

    $userData = $this->authManagerService->getUserData();

    if (!isset($userData) || !$this->authManagerService->canDeletePost($userData, $postId)) {
    
      $response = 'Cannot delete';

      $this->messages = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => $response,
      'generalMessage' => '',
      ];

      $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostFormView.php');
      $render->render($this->messages);
    }

    $this->postService->delete($postId, $userData->userId);

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostFormView.php');
    $render->render($this->messages);
  }

  public function addPostForm() {

    $this->messages = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostFormView.php');
    $render->render($this->messages);
  }

  public function editPostForm(int $postId) : Returntype {
    
  }

  private function getPostBySlug(string $slug){
    return $this->postService->getPostBySlug($slug);
  }
  private function getAllPosts(){
    return $this->postService->getAllPosts();
  }

}