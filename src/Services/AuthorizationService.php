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

  public function isRegularUserDashboardAllowed(UserIdentity $userData): bool {
    $permission = $this->userManagementService->getUserPermissionsById($userData->userId);

    return ($permission === UserPermissions::RegularUser->value) ? true : false;
  }

  public function canCreatePost(UserIdentity $userData): bool{
    $permission = $this->userManagementService->getUserPermissionsById($userData->userId);

    return ($permission === UserPermissions::RegularUser->value) ? true : false;
  }

  public function canDeletePost(UserIdentity $userData, int $postId) : bool {
    $ownerId = $this->postManagementService->getOwnershipById($postId);

    return $ownerId === $userData->userId ? true : false;
  }

  public function canEditPost(UserIdentity $userData, int $postId) {
    $ownerId = $this->postManagementService->getOwnershipById($postId);

    return $ownerId === $userData->userId ? true : false;
  }
  
}