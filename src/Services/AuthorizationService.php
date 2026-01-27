<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\UserManagementService;

final class AuthorizationService {

  private PostManagementService $postManagementService;
  private UserManagementService $userManagementService;

  public function __construct(PostManagementService $postManagementService, UserManagementService $userManagementService) {
    $this->postManagementService = $postManagementService;
    $this->userManagementService = $userManagementService;
  }

}