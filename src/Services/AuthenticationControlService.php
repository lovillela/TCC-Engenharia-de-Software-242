<?php

namespace Lovillela\BlogApp\Services;

class AuthenticationControlService {

  private string $activeUserQueryCheck = "SELECT `username`, `password` , `permissions`, `isActive` FROM `users` WHERE (isActive = ? AND username = ? AND permissions = ?)";

  public function authenticate(string $user, string $password, int $permission, int $isActive = 1) {

    global $connection;

    $sqlStatment = $connection->prepare($this->activeUserQueryCheck);
    $sqlStatment->bindValue(1, $isActive);
    $sqlStatment->bindValue(2, $user);
    $sqlStatment->bindValue(3, $permission);

    $queryResult = $sqlStatment->executeQuery();
    $userDataFromDB = $queryResult->fetchAllAssociative();
    
    if (!empty($userDataFromDB)) {
      $userData = $userDataFromDB[0];
      
      if(!$userData['isActive']){
        return false;
      }

      if(password_verify($password, $userData['password'])){
        unset($userData['password'], $password);
        return $userData;
      }
    }
    return false;
  }
}