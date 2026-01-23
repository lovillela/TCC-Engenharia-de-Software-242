<?php 

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;

final class AuthController{

  private AuthManagerService $authManagerService;

  public function __construct(array $dependencyContainer) {
    $this->authManagerService = $dependencyContainer['AuthManagerService'];
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
      $_SESSION['userID'] = $userData['id'];
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