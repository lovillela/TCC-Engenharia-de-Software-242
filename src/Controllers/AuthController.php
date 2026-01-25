<?php 

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Config\UserPermissions\UserRole;

final class AuthController{

  private AuthManagerService $authManagerService;
  private RedirectService $redirectService;

  public function __construct(array $dependencyContainer) {
    $this->authManagerService = $dependencyContainer['AuthManagerService'];
    $this->redirectService = $dependencyContainer['RedirectService'];
  }

  public function login(){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(!$this->authManagerService->login($username, $password)){
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
    }

    unset($_POST['password'], $this->password);
    $userData = $this->authManagerService->getUserData();

    if ($userData->permissions->value === UserRole::RegularUser->value) {
      $this->redirectService->redirectToUserDashboard();
    }elseif ($userData->permissions->value === UserRole::Admin->value || 
            $userData->permissions->value === UserRole::Moderator->value) {
      $this->redirectService->redirectToAdminDashboard();
    }

  }

  public function logout() {
    $this->authManagerService->destroySession();
    $this->redirectService->redirectToHome();
  }
}