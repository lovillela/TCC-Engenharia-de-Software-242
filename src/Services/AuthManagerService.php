<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Services\SessionService;
use Lovillela\BlogApp\Services\AuthenticationControlService;
use Lovillela\BlogApp\Services\AuthorizationService;
use Lovillela\BlogApp\Models\Users\UserIdentity;

/**
 * Ponto de entrada para os serviços de Autenticação e Autorização.
 * Com base nos resultados, será permitido ou não o uso das funções de crud
 * Entrypoint for Authentication and Authorization services.
 * Base on the results, the used of crud functions will be allowed or not.
 */
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

  public function getUserData(): ?UserIdentity {
    return $this->sessionService->getUser();
  }

  public function isSessionActive(){
    return $this->sessionService->isActive();
  }

  public function isRegularUserDashboardAllowed(UserIdentity $userData) {
    return $this->authorizationService->isRegularUserDashboardAllowed($userData); 
  }

  public function destroySession() {
    $this->sessionService->destroy();
  }
    
}
