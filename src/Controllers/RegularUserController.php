<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Config\Views\ViewPath;
use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Services\UserManagementService;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Services\ViewRenderService;

final class RegularUserController extends BaseController{
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
  
    $headTitle = 'Login';

    $bodyData = [
      'title' => 'Login',
      'headerText' => 'Login',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_LOGIN_REGULAR_USER, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function signUpAction(){

    $headTitle = 'SignUp page';

    $bodyData = [
      'title' => 'SignUp page',
      'headerText' => 'SignUp page',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
    ];

    $username = trim($_POST['newUser']);
    $email = trim($_POST['newUserEmail']);
    $password = trim($_POST['newUserPassword']);

    if (empty($username) || empty($email) || empty($password) || 
        !$this->authManagerService->validateCsrfToken($_POST['csrfToken'])) {
      $this->redirectService->redirectToHome();
    }
    
    $response = $this->userManagementService->create($username, $password, 
                                              $email, UserPermissions::RegularUser->value);

    if ($response['status'] != true) {
      $bodyData['errorMessage'] = $response['message'];
    }else{
      $bodyData['generalMessage'] = $response['message'];
    }

    $viewData = $this->prepareView(ViewPath::FRONTEND_SIGNUP, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function signUpPage(){

    $headTitle = 'SignUp page';

    $bodyData = [
      'title' => 'SignUp page',
      'headerText' => 'SignUp page',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_SIGNUP, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  /**
   * Renders the user dashboard if logged in.
   * @return void
   */
  public function dashboard(){
    
    if (!$this->authManagerService->isSessionActive()) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
    }

    $userData = $this->authManagerService->getUserData();

    if (!$this->authManagerService->isRegularUserDashboardAllowed($userData)) {
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
    }

    $userPosts = $this->postService->getAllPostsIdsAndTitlesByUserId($userData->userId);

    $headTitle = 'Dashboard';

    $bodyData = [
      'title' => 'Dashboard',
      'headerText' => 'Dashboard',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
      'userPosts' => $userPosts,
      'postList' => ViewPath::PARTIAL_POST_LIST->getPath(),
      'deleteActionUrl' => '/dashboard/post/',
      ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_DASHBOARD_REGULARUSER, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
    
  }

}
