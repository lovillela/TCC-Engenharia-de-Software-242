<?php

namespace Lovillela\BlogApp\Config\Permissions;

enum UserPermissions: int {
    case Admin = 1;
    case Moderator = 2;
    case RegularUser = 3;
}