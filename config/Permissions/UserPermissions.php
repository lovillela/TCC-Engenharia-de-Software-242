<?php

namespace Lovillela\BlogApp\Config\UserPermissions;

enum UserRole: int {
    case Admin = 1;
    case Moderator = 2;
    case RegularUser = 3;
}