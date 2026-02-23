<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Services\SessionService;
use Lovillela\BlogApp\Services\AuthenticationControlService;
use Lovillela\BlogApp\Services\AuthorizationService;
use Lovillela\BlogApp\Models\Users\UserIdentity;
use Lovillela\BlogApp\Services\CsrfService;

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
  private CsrfService $csrfService;
  
  public function __construct(SessionService  $sessionService, 
                              AuthenticationControlService $authenticationService, 
                              AuthorizationService $authorizationService,
                              CsrfService $csrfService) {
    $this->sessionService = $sessionService;
    $this->authenticationService = $authenticationService;
    $this->authorizationService = $authorizationService;
    $this->csrfService = $csrfService;
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

  public function getCsrfToken(): ?string {
    return $this->sessionService->getCsrfToken();
  }

  public function setCsrfToken() {
    $this->sessionService->setCsrfToken($this->csrfService->generate());
  }

  public function validateCsrfToken(string $csrfToken): bool {
    return $this->csrfService->validate($csrfToken, $this->sessionService->getCsrfToken());
  }

  public function isSessionActive(): bool{
    return $this->sessionService->isActive();
  }

  public function isRegularUserDashboardAllowed(UserIdentity $userData) {
    return $this->authorizationService->isRegularUserDashboardAllowed($userData); 
  }

  public function canCreatePost(UserIdentity $userData) {
    return $this->authorizationService->canCreatePost($userData);
  }

  public function canDeletePost(UserIdentity $userData, int $postId) {
    return $this->authorizationService->canDeletePost($userData, $postId);
  }

  public function canEditPost(UserIdentity $userData, int $postId) {
    return $this->authorizationService->canEditPost($userData, $postId);
  }

  public function destroySession() {
    $this->sessionService->destroy();
  }
    
}
