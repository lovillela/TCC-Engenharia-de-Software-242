<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\CommentService;
use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Config\Views\ViewPath;

final class PostController extends BaseController{

  private PostManagementService $postService;
  private RedirectService $redirectService;
  private ViewRenderService $viewRenderService;
  private CommentService $commentService;
  private array $dependencyContainer;

  public function __construct(array $dependencyContainer) {
    parent::__construct($dependencyContainer);
    $this->dependencyContainer = $dependencyContainer;
    $this->postService = $this->dependencyContainer['PostManagementService'];
    $this->redirectService = $this->dependencyContainer['RedirectService'];
    $this->viewRenderService = $this->dependencyContainer['ViewRenderService'];
    $this->commentService = $this->dependencyContainer['CommentService'];
  }

  public function index() {

    $posts = $this->postService->getAllPosts();

    if (!isset($posts)) {
      $this->redirectService->redirectToHome();
      exit;
    }

    $headTitle = 'Todos os artigos';
    
    $bodyData = [
      'headerText' => 'Todos os artigos',
      'errorMessage' => '',
      'generalMessage' => '',
      'posts' => $posts,
      'noPostsNoticeText' => 'Nenhum post publicado ainda. Volte em breve!' ,
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POST_HOME, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function show($slug) {

    $post = $this->postService->getPostBySlug($slug);

    if (!isset($post)) {
      $this->redirectService->redirectToHome();
      exit;
    }

    $comments = $this->commentService->getPostComments($post['id']);
    $userData = $this->authManagerService->getUserData();
    $isLoggedIn = $this->authManagerService->isSessionActive();
    $isAdminOrModerator = isset($userData) && ($this->authManagerService->isAdmin($userData) || 
                                $this->authManagerService->isModerator($userData));


    $renderPartialViewData = [
        'comments' => $comments,
        'csrfToken' => $this->authManagerService->getCsrfToken(),
        'postId' => $post['id'],
        'isLoggedIn' => $isLoggedIn,
        'isAdminOrModerator' => $isAdminOrModerator,
        'replyButtonText' => 'Responder',
        'sendButtonText' => 'Enviar'
    ];

    $renderedComments = $this->viewRenderService->renderComments($renderPartialViewData);

    $headTitle = $post['title'];
    
    $bodyData = [
      'goBackToPostHomeButtonText' => 'Voltar para Artigos',
      'postId' => $post['id'],
      'title' => $post['title'],
      'content' => $post['content'],
      'comments' => $renderedComments,
      'commentView' => ViewPath::PARTIAL_COMMENTS->getPath(),
        'commentsBlockHeaderText' => 'Comentários',
        'commentActionButtonText' => 'Comentar',
        'replyButtonText' => 'Responder',
        'sendButtonText' > 'Enviar',
        'noCommentsText' => 'Nenhum comentário ainda.',
        'loginButtonText' => 'Faça <a href="/login/">login</a> para participar da discussão.',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
      'isLoggedIn' => $isLoggedIn,
      'loggedUserId' => $userData->userId ?? null,
      'isAdminOrModerator' => isset($userData) && ($this->authManagerService->isAdmin($userData) || $this->authManagerService->isModerator($userData)),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POST, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
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

    if (!isset($response) || $response['status'] === false) {
      $this->redirectService->redirectToUserDashboard();
      exit;
    }

    $headTitle = 'Add Post';

    $bodyData = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => $response['message'],
      'generalMessage' => '',
      'text' => $response['text'],
      'textEditor' => ViewPath::PARTIAL_TEXT_EDITOR->getPath(),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function createCommentAction() {
  
    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || !$this->authManagerService->validateCsrfToken($_POST['csrfToken'])) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $userId = $userData->userId;
    $postId = $_POST['postId'];
    $commentContent = trim($_POST['commentContent']);
    $parentId = !empty($_POST['parentId']) ? $_POST['parentId'] : null;

    $this->commentService->create($userId, $postId, $commentContent, $parentId);

    $postSlugToRedirectTo = $this->postService->getPostSlug($postId);
    
    $this->redirectService->redirectToPostBySlug($postSlugToRedirectTo);
    exit;
  }

  public function deletePostAction(int $postId) {

    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || !$this->authManagerService->canDeletePost($userData, $postId) 
        || !$this->authManagerService->validateCsrfToken($_POST['csrfToken'])) {

      $this->redirectService->redirectToUserDashboard();
      exit;
    }

    $this->postService->delete($postId, $userData->userId);
    $this->redirectService->redirectToUserDashboard();
    exit;
    
  }

  public function deleteCommentAction() {
    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || !$this->authManagerService->validateCsrfToken($_POST['csrfToken']) ||
          (!$this->authManagerService->isAdmin($userData) &&  !$this->authManagerService->isModerator($userData))
      ) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $commentId = $_POST['commentId'];
    $postId = $_POST['postId'];

    $this->commentService->delete($commentId);
    $postSlugToRedirectTo = $this->postService->getPostSlug($postId);
    
    $this->redirectService->redirectToPostBySlug($postSlugToRedirectTo);
    exit;  
  
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
      'textEditor' => ViewPath::PARTIAL_TEXT_EDITOR->getPath(),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function editPostForm(int $postId) {

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
      'postId' => $postContent['id'],
      'textEditor' => ViewPath::PARTIAL_TEXT_EDITOR->getPath(),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_EDIT_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function editPostAction(int $postId) {
    
    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || !isset($userData) 
        || !$this->authManagerService->canEditPost($userData, $postId) 
        || !$this->authManagerService->validateCsrfToken($_POST['csrfToken']) ) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $postContent = $this->postService->getPostById($postId);

    if (!isset($postContent)) {
      $this->redirectService->redirectToUserDashboard();
      exit;
    }

    $title = $_POST['postTitle'];
    $text = $_POST['blogPost'];
    $slug = $_POST['slugUrl'];
    $postId = $_POST['postId'];

    $response = $this->postService->update($title, $text, $slug, $postId);

    $headTitle = 'Edit Post';

    $bodyData = [
      'title' => 'Edit Post',
      'headerText' => 'Edit Post',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
      'postTitle' => $response['title'],
      'slugUrl' => $response['slug'],
      'blogPost' => $response['text'],
      'postId' => $response['postId'],
      'status' => $response['status'],
      'message' => $response['message'],
      'textEditor' => ViewPath::PARTIAL_TEXT_EDITOR->getPath(),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_EDIT_POSTFORM, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

}