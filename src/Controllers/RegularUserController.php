<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\UserManagementService;
use Lovillela\BlogApp\Services\ViewRenderService;

final class RegularUserController{
  private array $messages;
  private int $role = 3;

  public function __construct(array $dependencyContainer) {
    $this->dependencyContainer = $dependencyContainer;
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
      header('Location: /signup/');
      exit();
    }

    $userMgnt = new UserManagementService();
    $response = $userMgnt->createUser($username, $password, $email, $this->role);

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
    
    $this->regularUserCheck();

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/DashBoardViewRegularUser.php');
    $render->render($this->messages);
  }

  private static function regularUserCheck()  {
    if ($_SESSION['role'] != 3) {
      session_destroy();
      header('Location: /');
      exit();
    }
  }
}
