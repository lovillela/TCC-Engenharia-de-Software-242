<?php 

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthenticationControlService;

final class AuthController{

  private $username;
  private $password;

  private $authenticationService;
  private $premission;

  public function __construct(int $premission = 0) {
    $this->authenticationService = new AuthenticationControlService();
    $this->premission = $premission;
  }

  public function login(){
    $this->username = trim($_POST['username']);
    $this->password = trim($_POST['password']);

    if(empty($this->username) || empty($this->password)){
      session_destroy();
      header('Location: /');
      exit();
    }

    $userData = $this->authenticationService->authenticate($this->username, $this->password, $this->premission);
    unset($_POST['password'], $this->password);

    if (!$userData && ($this->premission >= 1 && $this->premission <= 3)) {
      session_destroy();
      if ($this->premission >= 1 && $this->premission <= 2) {
        header('Location: /admin/');
        exit();
      }else{
        header('Location: /login/');
        exit();
      }

    }else{
      session_regenerate_id(true); // Regenerate session ID after successful login
      $_SESSION['user'] = $userData['username'];
      $_SESSION['role'] = $userData['permissions'];

      if ($this->premission >= 1 && $this->premission <= 2) {
        header('Location: /admin/dashboard/');
        exit();
      }else{
        header('Location: /dashboard/');
        exit();
      }
    }
  }

  public static function logout() {
    session_regenerate_id(true);
    session_destroy();
    header('Location: /');
    exit();
  }
}