<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Services\SessionService;
use Lovillela\BlogApp\Services\AuthenticationControlService;
use Lovillela\BlogApp\Services\AuthorizationService;
use Lovillela\BlogApp\Models\Users\UserIdentity;

final class AuthManagerService {

  private SessionService $sessionService;
  private AuthenticationControlService $authenticationService;
  private AuthorizationService $authorizationService;

  public function __construct(SessionService  $sessionService, 
                              AuthenticationControlService $authenticationService, 
                              AuthorizationService $authorizationService) {
    $this->sessionService = $sessionService;
    $this->authenticationService = $authenticationService;
    $this->authorizationService = $authorizationService;

  }

  public function login(string $email, string $password) : bool {
    
    $userIdentity = $this->authenticationService->authenticate($email, $password);

    if (!isset($userIdentity)) {
      return false;
    }

    $this->sessionService->regenerate();
    $this->sessionService->setUser($userIdentity);

    return true;
  }

  public function destroySession() {
    $this->sessionService->destroy();
  }
    
}
