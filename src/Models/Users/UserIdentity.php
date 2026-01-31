<?php

namespace Lovillela\BlogApp\Models\Users;

use Lovillela\BlogApp\Config\Permissions\UserPermissions;

final class UserIdentity{
  public readonly int $userId;
  public readonly string $userName;
  public readonly UserPermissions $permissions;

  public function __construct(int $userId, string $userName, UserPermissions $permissions) {
    $this->userId = $userId;
    $this->userName = $userName;
    $this->permissions = $permissions;
  }
}