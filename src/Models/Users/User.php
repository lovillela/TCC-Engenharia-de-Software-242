<?php

use Doctrine\DBAL\Types\BigIntType;

class User{
    private int $id;
    private string $username;
    private string $email;
    private int $permissions;
    private string $password;
    private bool $isActive;
}