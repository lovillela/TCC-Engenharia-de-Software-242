<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Models\Users\UserIdentity;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\UserManagementService;

final class AuthorizationService {

  private PostManagementService $postManagementService;
  private UserManagementService $userManagementService;

  public function __construct(PostManagementService $postManagementService, UserManagementService $userManagementService) {
    $this->postManagementService = $postManagementService;
    $this->userManagementService = $userManagementService;
  }

  public function isRegularUserDashboardAllowed(UserIdentity $userData) {
    $permission = $this->userManagementService->getUserPermissionsById($userData->userId);

    if ($permission !== UserPermissions::RegularUser->value) {
      return false;
    }

    return true;
  }
}