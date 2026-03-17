<?php 

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\RedirectService;

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

    if ($this->authManagerService->isRegularUserDashboardAllowed($userData)) {
      $this->redirectService->redirectToUserDashboard();
    }elseif ($this->authManagerService->isAdmin($userData) || 
            $this->authManagerService->isModerator($userData)) {
      $this->redirectService->redirectToAdminDashboard();
    }

  }

  public function logout() {
    $this->authManagerService->destroySession();
    $this->redirectService->redirectToHome();
  }
}