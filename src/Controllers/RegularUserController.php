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
  private UserManagementService $userManagementService;
  private RedirectService $redirectService;
  private ViewRenderService $viewRenderService;
  private PostManagementService $postService;

  public function __construct(array $dependencyContainer) {
    parent::__construct($dependencyContainer);
    $this->userManagementService = $dependencyContainer['UserService'];
    $this->redirectService = $dependencyContainer['RedirectService'];
    $this->viewRenderService = $dependencyContainer['ViewRenderService'];
    $this->postService = $dependencyContainer['PostManagementService'];
  }
  
  public function index() {
  
    $headTitle = 'Login';

    $bodyData = [
      'headerText' => 'Login',
      'errorMessage' => '',
      'generalMessage' => '',
      'returnHomeLinkText' => 'Voltar para Home',
      'userLabel' => 'Usuário',
      'passwordLabel'=> 'Senha',
      'loginButtonText' => 'Entrar',
      'signUpLoginText' => 'Não tem uma conta? Cadastre-se',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
    ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_LOGIN_REGULAR_USER, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function signUpAction(){

    $headTitle = 'Cadastro';

    $bodyData = [
      'headerText' => 'Cadastro',
      'errorMessage' => '',
      'generalMessage' => '',
      'returnHomeLinkText' => 'Voltar para Home',
      'userLabel' => 'Usuário',
      'emailLabel' => 'E-mail',
      'passwordLabel' => 'Senha',
      'signUpButtonText' => 'Cadastrar',
      'alreadyRegisteredLoginText' => 'Já tem uma conta? Faça Login',
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

    $headTitle = 'Cadastro';

    $bodyData = [
      'headerText' => 'Cadastro',
      'errorMessage' => '',
      'generalMessage' => '',
      'returnHomeLinkText' => 'Voltar para Home',
      'userLabel' => 'Usuário',
      'emailLabel' => 'E-mail',
      'passwordLabel' => 'Senha',
      'signUpButtonText' => 'Cadastrar',
      'alreadyRegisteredLoginText' => 'Já tem uma conta? Faça Login',
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
      'headerText' => 'Dashboard',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
      'userPosts' => $userPosts,
      'postList' => ViewPath::PARTIAL_POST_LIST->getPath(),
        'noPostsNoticeText' => 'Não há artigos cadastrados!',
        'editButtonText' => 'Editar',
        'deleteButtonText' => 'Deletar',
        'tableHeaderPostTitleText' => 'Título do Post',
        'tableHeaderActionText' => 'Ações',
        'hideEditButton' => false,
      'deleteActionUrl' => '/dashboard/post/',
      ];

    $viewData = $this->prepareView(ViewPath::FRONTEND_DASHBOARD_REGULARUSER, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
    
  }

}
