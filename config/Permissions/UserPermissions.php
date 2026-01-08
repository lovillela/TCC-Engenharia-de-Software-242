<?php

namespace Lovillela\BlogApp\UserPermissions;

enum UserRole: int {
    case Admin = 1;
    case Moderator = 2;
    case RegularUser = 3;
}