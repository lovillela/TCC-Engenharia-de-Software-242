<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Services\SessionService;
use Lovillela\BlogApp\Services\AuthenticationControlService;
use Lovillela\BlogApp\Services\AuthorizationService;
use Lovillela\BlogApp\Models\Users\UserIdentity;
use Lovillela\BlogApp\Services\CsrfService;
use Psr\Log\LoggerInterface;

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
  private LoggerInterface $logger;
  
  public function __construct(SessionService  $sessionService, 
                              AuthenticationControlService $authenticationService, 
                              AuthorizationService $authorizationService,
                              CsrfService $csrfService,
                              LoggerInterface $logger) {
    $this->sessionService = $sessionService;
    $this->authenticationService = $authenticationService;
    $this->authorizationService = $authorizationService;
    $this->csrfService = $csrfService;
    $this->logger = $logger;
  }

  public function login(string $email, string $password) : bool {
    
    $userIdentity = $this->authenticationService->authenticate($email, $password);

    if (!isset($userIdentity)) {
      $this->logger->warning('Falha no login - UserIdentity não foi definido',  ['email' => $email]);
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

    if (!$this->csrfService->validate($csrfToken, $this->sessionService->getCsrfToken())) {
      $this->logger->warning('Falha ao validar o csrfToken! Acão bloqueada!', ['csrfToken' => $csrfToken]);
      return false;
    }

    return true;
  }

  public function isSessionActive(): bool{
    return $this->sessionService->isActive();
  }

  public function isRegularUserDashboardAllowed(UserIdentity $userData): bool {

    if (!$this->authorizationService->isRegularUserDashboardAllowed($userData)) {
      $this->logger->alert('Acesso negado ao acessar dashboard de usuário', ['userId' => $userData->userId]);
      return false;
    }

    return true;
  }

  public function isAdmin(UserIdentity $userData) :bool {
    if (!$this->authorizationService->isAdmin($userData)) {
      $this->logger->alert('Acesso negado à função administrativa!', ['userId' => $userData->userId]);
      return false;
    }

    return true;
  }

    public function isModerator(UserIdentity $userData) :bool {
    if (!$this->authorizationService->isModerator($userData)) {
      $this->logger->alert('Acesso negado à função de moderação!', ['userId' => $userData->userId]);
      return false;
    }

    return true;
  }

  public function canCreatePost(UserIdentity $userData) {

    if (!$this->authorizationService->canCreatePost($userData)) {
      $this->logger->alert('Acesso negado ao criar post', ['userId' => $userData->userId]);
      return false;
    }

    return true;
  }

  public function canDeletePost(UserIdentity $userData, int $postId) {

    if (!$this->authorizationService->canDeletePost($userData, $postId) &&
        !$this->authorizationService->isAdmin($userData)) {
      $this->logger->alert('Acesso negado ao deletar post', ['userId' => $userData->userId, 'postId' => $postId]);
      return false;
    }

    return true;
  }

  public function canEditPost(UserIdentity $userData, int $postId) {

    if (!$this->authorizationService->canEditPost($userData, $postId)) {
      $this->logger->alert('Acesso negado ao editar post', ['userId' => $userData->userId, 'postId' => $postId]);
      return false;
    }

    return true;  
  }

  public function destroySession() {
    $this->sessionService->destroy();
  }
    
}
