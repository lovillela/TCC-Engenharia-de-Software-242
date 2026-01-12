<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Repositories\UserRepository;

class AuthenticationControlService {

  private UserRepository $userRepository;

  public function __construct(UserRepository $userRepository){
    $this->userRepository = $userRepository;
  }

  public function authenticate(string $email, string $password): ?array {

    $user = $this->userRepository->findByEmail($email);

    if(!$user || !(password_verify($password, $user['password']))){
      return null;
    }

    unset($password, $user['password']);
    
    return $user;
  }
}