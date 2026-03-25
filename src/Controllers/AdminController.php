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
    
    $headTitle = 'Acesso Admin';

    $bodyData=[
      'loginHeaderText' => 'Acesso Admin',
      'errorMessage' => '',
      'returnHomeLinkText' => 'Voltar para Home',
      'userLabel' => 'Usuário Admin',
      'passwordLabel' => 'Senha Admin',
      'loginButtonText' => 'Acesso Admin',
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
      'postListView' => ViewPath::PARTIAL_POST_LIST->getPath(),
        'noPostsNoticeText' => 'Não há artigos cadastrados!',
        'editButtonText' => 'Editar',
        'deleteButtonText' => 'Deletar',
        'tableHeaderPostTitleText' => 'Título do Post',
        'tableHeaderActionText' => 'Ações Administrativas',      
        'hideEditButton' => true,
      'deleteUrlAction' => '/admin/dashboard/post/',
      ];

    $viewData = $this->prepareView(ViewPath::ADMIN_LIST_ALL_USERS_POSTS, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function getAllUsers() {
    $userData = $this->authManagerService->getUserData();

    if (!isset($userData) ||
        !$this->authManagerService->isSessionActive() ||
        !$this->authManagerService->isAdmin($userData) ||
        !$this->authManagerService->isSessionActive()) {
      $this->redirectService->redirectToHome();
    }

    $allUsersList = $this->userManagementService->getAllUsers();
    
    $headTitle = 'Dashboard - Usuários';
    $bodyData = [
      'headerText' => 'Dashboard',
      'errorMessage' => '',
      'generalMessage' => '',
      'csrfToken' => $this->authManagerService->getCsrfToken(),
      'users' => $allUsersList,
      'userListView' => ViewPath::PARTIAL_USER_LIST->getPath(),
        'tableHeaderIdText' => 'Id',
        'tableHeaderUsernameText' => 'Usuário',
        'tableHeaderEmailText' => 'E-mail',
        'tableHeaderPermissionText' => 'Função',
        'tableHeaderActionText' => 'Ações Administrativas',
        'noUsersNoticeText' => 'Nenhum usuário registrado.',
        'deleteButtonText' => 'Deletar Usuário',
      'deleteUrlAction' => '/admin/dashboard/user/',
      ];

    $viewData = $this->prepareView(ViewPath::ADMIN_LIST_ALL_USERS, $headTitle, $bodyData);
    $this->viewRenderService->render($viewData);
  }

  public function deleteUserAction(int $userId){
    $userData = $this->authManagerService->getUserData();
    
    if (!isset($userData) ||
        !$this->authManagerService->isSessionActive() ||
        !$this->authManagerService->isAdmin($userData) ||
        !$this->authManagerService->validateCsrfToken($_POST['csrfToken'])) {
      
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }

    $this->userManagementService->delete($userId);

    $this->redirectService->redirectToAdminUsersList();
  }

  public function deletePostByAdminAction(int $postId) {
    $userData = $this->authManagerService->getUserData();

    
    if (!isset($userData) ||
        !$this->authManagerService->isSessionActive() ||
        !$this->authManagerService->isAdmin($userData) ||
        !$this->authManagerService->validateCsrfToken($_POST['csrfToken'])) {
      
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
      exit;
    }
    $this->postService->deletePostByAdmin($postId);
    $this->redirectService->redirectToAdminPostsList();
  }
 }