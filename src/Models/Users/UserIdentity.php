<?php

namespace Lovillela\BlogApp\Models\UserIdentity;

use Lovillela\BlogApp\Config\UserPermissions\UserRole;

final class UserIdentity{
  public readonly int $userId;
  public readonly string $userName;
  public readonly UserRole $permissions;

  public function __construct(int $userId, string $userName, UserRole $permissions) {
    $this->userId = $userId;
    $this->userName = $userName;
    $this->permissions = $permissions;
  }
}