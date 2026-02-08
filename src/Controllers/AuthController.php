<?php 

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;

final class AuthController{

  private AuthManagerService $authManagerService;
  private RedirectService $redirectService;

  public function __construct(array $dependencyContainer) {
    $this->authManagerService = $dependencyContainer['AuthManagerService'];
    $this->redirectService = $dependencyContainer['RedirectService'];
  }

  public function login(){

    if (!isset($_POST['csrfToken']) || !$this->authManagerService->validateCsrfToken($_POST['csrfToken'])) {
      $this->redirectService->redirectToHome();
    }

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(!$this->authManagerService->login($username, $password)){
      $this->authManagerService->destroySession();
      $this->redirectService->redirectToHome();
    }

    unset($_POST['password'], $this->password);
    $userData = $this->authManagerService->getUserData();

    if ($userData->permissions->value === UserPermissions::RegularUser->value) {
      $this->redirectService->redirectToUserDashboard();
    }elseif ($userData->permissions->value === UserPermissions::Admin->value || 
            $userData->permissions->value === UserPermissions::Moderator->value) {
      $this->redirectService->redirectToAdminDashboard();
    }

  }

  public function logout() {
    $this->authManagerService->destroySession();
    $this->redirectService->redirectToHome();
  }
}