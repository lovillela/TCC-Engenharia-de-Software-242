<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Models\Views\ViewData;
use Lovillela\BlogApp\Config\Views\ViewPath;

final class PostController extends BaseController{

  private PostManagementService $postService;
  private RedirectService $redirectService;
  private AuthManagerService $authManagerService;
  private ViewRenderService $viewRenderService;
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

    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || !$this->authManagerService->canCreatePost($userData) 
        || !$this->authManagerService->validateCsrfToken($_POST['csrfToken'])) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }
 
    $title = $_POST['postTitle'];
    $text = $_POST['blogPost'];
    
    $response = $this->postService->create($title, $text, $userData->userId);

    $headTitle = 'Add Post';

    $bodyData = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => $response['Message'],
      'generalMessage' => '',
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function deletePostAction(int $postId) {

    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || !$this->authManagerService->canDeletePost($userData, $postId) 
        || !$this->authManagerService->validateCsrfToken($_POST['csrfToken'])) {

      $headTitle = 'Post Form';

      $bodyData = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => 'Cannot Delete',
      'generalMessage' => '',
      ];

      $viewData = $this->prepareView(ViewPath::FRONTEND_POSTFORM, $headTitle, $bodyData);
      $this->viewRenderService->render($viewData);

      exit;
      
    }

    $this->postService->delete($postId, $userData->userId);

    $headTitle = 'Post Form';
      
      $bodyData = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => '',
      'generalMessage' => 'Deleted',
      ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function addPostForm() {

    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || !$this->authManagerService->canCreatePost($userData)) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $headTitle = 'Post Form';

    $bodyData = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function editPostForm(int $postId) {

    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || !$this->postService->getOwnershipById($postId)) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $postContent = $this->postService->getPostById($postId);

    if (!isset($postContent)) {
      $this->redirectService->redirectToUserDashboard();
      exit;
    }

    $headTitle = 'Edit Post';

    $bodyData = [
      'title' => 'Edit Post',
      'headerText' => 'Edit Post',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
      'postTitle' => $postContent['title'],
      'slugUrl' => $postContent['slug'],
      'blogPost' => $postContent['content'],
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_EDIT_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function editPostAction(int $postId) {
    
    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || $this->postService->getOwnershipById($postId) !== $userData->userId) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $postContent = $this->postService->getPostById($postId);

    if (!isset($postContent)) {
      $this->redirectService->redirectToUserDashboard();
      exit;
    }

    $headTitle = 'Edit Post';

    $bodyData = [
      'title' => 'Edit Post',
      'headerText' => 'Edit Post',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
      'postTitle' => $postContent['title'],
      'slugUrl' => $postContent['slug'],
      'blogPost' => $postContent['content'],
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_EDIT_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  private function getPostBySlug(string $slug){
    return $this->postService->getPostBySlug($slug);
  }
  private function getAllPosts(){
    return $this->postService->getAllPosts();
  }

}