<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\UserManagementService;
use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Services\RedirectService;

final class AdminController{

  private array $messages;
  private $render;
  public function index() {
    
    $this->messages=[
      'title' => 'Admin Login',
      'loginHeaderText' => 'Admin Login Page',
      'errorMessage' => '',
    ];

    $viewRender = new ViewRenderService(__DIR__ . '/../Views/Admin/LoginView.php');
    $viewRender->render(messages: $this->messages);
  }

  public function dashboard(){
    $this->messages=[
      'title' => 'Admin Dashboard',
      'headerText' => 'Admin Dashboard',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $userManagement = new UserManagementService();
    
    if(!($userManagement->adminPrivilegeCheck())){
      //User session already destroyed on adminPrivilegeCheck
      RedirectService::redirectToHome();
      exit();
    }

    $viewRender = new ViewRenderService(__DIR__ . '/../Views/Admin/DashBoardView.php');
    $viewRender->render(messages: $this->messages);
  }

  public function userCreatorForm(){
    $this->messages=[
      'title' => 'User Creator',
      'headerText' => 'User Creator',
      'errorMessage' => '', 
    ];

    $userManagement = new UserManagementService();
    
    if(!($userManagement->adminPrivilegeCheck())){
      //User session already destroyed on adminPrivilegeCheck
      RedirectService::redirectToHome();
      exit();
    }

    $viewRender = new ViewRenderService(__DIR__ . '/../Views/Admin/AddUserView.php');
    $viewRender->render($this->messages);
  }

  public function createUser() {

    $this->messages=[
      'title' => 'User Creator',
      'headerText' => 'User Creator',
      'errorMessage' => '', 
    ];

    $username = trim($_POST['newUser']);
    $password = trim($_POST['newUserPassword']);
    $email = trim($_POST['newUserEmail']);
    $role = $_POST['userRole'];

    $userMngt = new UserManagementService();

    $response = $userMngt->createUser($username, $password, $email, $role);

    if ($response['Status'] != 1) {
      $this->messages['errorMessage'] = $response['Message'];
    }else{
      $this->messages['generalMessage'] = $response['Message'];
    }

    $viewRender = new ViewRenderService(__DIR__ . '/../Views/Admin/AddUserView.php');
    $viewRender->render($this->messages);

  }

 }