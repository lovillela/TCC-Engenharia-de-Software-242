<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Services\UserManagementService;
use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Config\Views\ViewPath;
use Lovillela\BlogApp\Services\PostManagementService;

final class AdminController extends BaseController{

  private array $dependencyContainer;
  private UserManagementService $userManagementService;
  private RedirectService $redirectService;
  private AuthManagerService $authManagerService;
  private ViewRenderService $viewRenderService;
  private PostManagementService $postService;
  
  public function __construct(array $dependencyContainer) {
    $this->dependencyContainer = $dependencyContainer;
    $this->userManagementService = $this->dependencyContainer['UserService'];
    $this->redirectService = $this->dependencyContainer['RedirectService'];
    $this->authManagerService = $this->dependencyContainer['AuthManagerService'];
    $this->viewRenderService = $this->dependencyContainer['ViewRenderService'];
    $this->postService = $this->dependencyContainer['PostManagementService'];
  }
  public function index() {
    
    $headTitle = 'Admin Login';

    $bodyData=[
      'title' => 'Admin Login',
      'loginHeaderText' => 'Admin Login Page',
      'errorMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
    ];

    $viewData = $this->prepareView(ViewPath::ADMIN_LOGIN, $headTitle, $bodyData);

    $this->viewRenderService->render($viewData);
  }

  public function dashboard(){
  
    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isSessionActive() || 
        !isset($userData) || 
        !$this->authManagerService->isAdmin($userData)){
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }
  
    $headTitle = 'Admin Dashboard';

    $bodyData = [
      'title' => 'Admin Dashboard',
      'headerText' => 'Admin Dashboard',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
    ];

    $viewData = $this->prepareView(ViewPath::ADMIN_DASHBOARD, $headTitle, $bodyData);

    $this->viewRenderService->render($viewData);
    
  }

  public function userCreatorForm(){
    
    if (!$this->authManagerService->isSessionActive() ||
        !$this->authManagerService->isAdmin($this->authManagerService->getUserData())) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $headTitle = 'User Creator';

    $bodyData=[
      'title' => 'User Creator',
      'headerText' => 'User Creator',
      'errorMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(), 
    ];

    $viewData = $this->prepareView(ViewPath::ADMIN_ADD_USER, $headTitle, $bodyData);

    $this->viewRenderService->render($viewData);
        
  }

  public function createUserAction() {

    if (!$this->authManagerService->isSessionActive() || 
        !$this->authManagerService->validateCsrfToken($_POST['csrfToken']) ||
        !$this->authManagerService->isAdmin($this->authManagerService->getUserData())) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $headTitle = 'User Creator';

    $bodyData=[
      'title' => 'User Creator',
      'headerText' => 'User Creator',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
    ];

    $username = trim($_POST['newUser']);
    $password = trim($_POST['newUserPassword']);
    $email = trim($_POST['newUserEmail']);

    $response = $this->userManagementService->create($username, $password, 
                                              $email, UserPermissions::Admin->value);

    if (!$response['status']) {
      $bodyData['errorMessage'] = $response['message'];
    }else{
      $bodyData['generalMessage'] = $response['message'];
    }

    $viewData = $this->prepareView(ViewPath::ADMIN_ADD_USER, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);

  }

  public function getAllUsersPosts() {
    $userData = $this->authManagerService->getUserData();

    if (!isset($userData) ||
        !$this->authManagerService->isSessionActive() ||
        !$this->authManagerService->isAdmin($userData) ||
        !$this->authManagerService->isSessionActive()) {
      $this->redirectService->redirectToHome();
    }

    $allUserPostsList = $this->postService->getAllPostsIdsAndTitlesForAdmin();

    $headTitle = 'Dashboard';

    $bodyData = [
      'title' => 'Dashboard',
      'headerText' => 'Dashboard',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
      'userPosts' => $allUserPostsList,
      'postList' => ViewPath::PARTIAL_POST_LIST->getPath(),
      ];

    $viewData = $this->prepareView(ViewPath::ADMIN_LIST_ALL_USERS_POSTS, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }
 }