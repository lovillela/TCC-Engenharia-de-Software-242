<?php

namespace Lovillela\BlogApp\Utils;

final class PasswordHash{
  private static string $hashAlgorithm = PASSWORD_BCRYPT;

  public static function hashPassword(string $password): string  {
    return (password_hash($password, self::$hashAlgorithm));
  }
}

