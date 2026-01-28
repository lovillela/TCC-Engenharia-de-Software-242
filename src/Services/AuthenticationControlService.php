<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Repositories\UserRepository;
use Lovillela\BlogApp\Models\Users\UserIdentity;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;

class AuthenticationControlService {

  private UserManagementService $userManagementService;
  
  public function __construct(UserManagementService $userManagementService){
    $this->userManagementService = $userManagementService;
  }

  public function authenticate(string $email, string $password): ?UserIdentity {

    $user = $this->userManagementService->findByEmail($email);

    if (!$user) {
      return null;
    }
    
    $permissions =  UserPermissions::tryFrom($user['permissions']);
    
    if(!(password_verify($password, $user['password'])) || !$permissions){
      return null;
    }

    unset($password, $user['password']);
    
    return new UserIdentity($user['id'], $user['username'], $permissions);
  }
}