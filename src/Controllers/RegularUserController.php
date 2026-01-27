<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Services\UserManagementService;
use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;

final class RegularUserController{
  private array $messages;
  private array $dependencyContainer;
  private UserManagementService $userManagementService;
  private RedirectService $redirectService;

  public function __construct(array $dependencyContainer) {
    $this->dependencyContainer = $dependencyContainer;
    $this->userManagementService = $this->dependencyContainer['UserService'];
    $this->redirectService = $this->dependencyContainer['RedirectService'];
  }
  
  public function index() {
    $this->messages = [
      'title' => 'Login',
      'headerText' => 'Login',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $viewRender = new ViewRenderService(__DIR__ . '/../Views/Frontend/LoginViewRegularUser.php');
    $viewRender->render($this->messages);
  }

  public function signUpAction(){

    $this->messages = [
      'title' => 'SignUp page',
      'headerText' => 'SignUp page',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $username = trim($_POST['newUser']);
    $email = trim($_POST['newUserEmail']);
    $password = trim($_POST['newUserPassword']);

    if (empty($username) || empty($email) || empty($password)) {
      $this->redirectService->redirectToHome();
    }
    
    $response = $this->userManagementService->create($username, $password, 
                                              $email, UserPermissions::RegularUser->value);

    if ($response['Status'] != 1) {
      $this->messages['errorMessage'] = $response['Message'];
    }else{
      $this->messages['generalMessage'] = $response['Message'];
    }

    $viewRender = new ViewRenderService(__DIR__ . '/../Views/Frontend/SignupView.php');
    $viewRender->render($this->messages);
  }

  public function signUpPage(){

    $this->messages = [
      'title' => 'SignUp page',
      'headerText' => 'SignUp page',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $viewRender = new ViewRenderService(__DIR__ . '/../Views/Frontend/SignupView.php');
    $viewRender->render($this->messages);
  }

  /**
   * Renders the user dashboard if logged in.
   * @return void
   */
  public function dashboard(){
    $this->messages = [
      'title' => 'Dashboard',
      'headerText' => 'Dashboard',
      'errorMessage' => '',
      'generalMessage' => '',
    ];
    
    /**
     * Verificar Autorização
     * Check authorization
     */

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/DashBoardViewRegularUser.php');
    $render->render($this->messages);
  }

}
