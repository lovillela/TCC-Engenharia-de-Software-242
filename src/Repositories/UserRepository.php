<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;

final class UserRepository
{
  private Connection $connection;

  //Deletes
  private string $userCreatorQuery = 'INSERT INTO `users` (`username`, `email`, `password`, `permissions`, `isActive` ) 
                                      VALUES (?, ?, ?, ?, 1)';

  //Selects
  private string $userPasswordQuery = 'SELECT `password` FROM `users` WHERE `username` = ?';
  private string $activeUserQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE (`username` = ?) AND (isActive = 1)) as isActive';
  private string $userExistsQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE `username` = ?) as usernameExists';
  private string $emailExistsQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE `email` = ?) as emailExists';
  private string $checkUserCurrentSession = 'SELECT * FROM `users` WHERE `username` = ?';
  private string $getUserInfoQueryEmail = 'SELECT `id`, `username`, `email`, `password`, `isActive` FROM 
                                          `users` WHERE (`email` = ? AND isActive = 1)';
  private string $getUserInfoQueryUsername = 'SELECT `id`, `username`, `email`, `password`, `isActive` FROM 
                                              `users` WHERE (`username` = ? AND isActive = 1)';
  private string $getUserIdByUsernameQuery = 'SELECT `id` FROM `users` WHERE `username` = ?';
  private string $getUserPostsQuery = 'SELECT `id_post` FROM `post_users` WHERE `id_user` = ?';
  private string $getUserPermissionsByIdQuery = 'SELECT `permissions` FROM `users` WHERE `id_user` = ?';

  //Deletes
  private string $deleteUserQuery = 'DELETE FROM `users` WHERE `id` = ?';
  private string $deleteUserbyUsernameQuery = 'DELETE FROM `users` WHERE `username` = ?';
  
  public function __construct(Connection $connection){
      $this->connection = $connection;
  }

  public function create(string $username, string $password, string $email, int $role /**Account role to be created*/): bool {
    
    $sqlStatment_UserCreation = $this->connection->prepare($this->userCreatorQuery);
    $sqlStatment_UserCreation->bindValue(1, $username);
    $sqlStatment_UserCreation->bindValue(2, $email);
    $sqlStatment_UserCreation->bindValue(3, $password);
    $sqlStatment_UserCreation->bindValue(4, $role);

    return (bool) $sqlStatment_UserCreation->executeStatement();
  }

  public function delete(int $userId): bool {

    $deleteUserStmt = $this->connection->prepare($this->deleteUserQuery);
    $deleteUserStmt->bindValue(1, $userId);
    
    return (bool) $deleteUserStmt->executeStatement();
  }

  public function exists(string $username): bool {

    $queryUserExists = $this->connection->prepare($this->userExistsQuery);
    $queryUserExists->bindValue(1, $username);

    return (bool) $queryUserExists->executeQuery()->fetchOne();
  }

  public function emailExists(string $email): bool {

    $emailExistsStmt = $this->connection->prepare($this->emailExistsQuery);
    $emailExistsStmt->bindValue(1, $email);

    return (bool) $emailExistsStmt->executeQuery()->fetchOne();
  }

  public function findByUsername(string $username) : ?array {
    $getUserInfoStmt = $this->connection->prepare($this->getUserInfoQueryUsername);
    $getUserInfoStmt->bindValue(1, $username);

    return $getUserInfoStmt->executeQuery()->fetchAssociative() ?: null;
  }

  public function findByEmail(string $email) : ?array {
    $getUserInfoStmt = $this->connection->prepare($this->getUserInfoQueryEmail);
    $getUserInfoStmt->bindValue(1, $email);

    return $getUserInfoStmt->executeQuery()->fetchAssociative() ?: null;
  }

  public function findIdByUsername(string $username) {
    $getUserIdStmt = $this->connection->prepare($this->getUserIdByUsernameQuery);
    $getUserIdStmt->bindValue(1, $username);
    return $getUserIdStmt->executeQuery()->fetchOne() ?: false;
  }
  
  public function isActive(string $username) : bool {
    
    $activeUserStmt = $this->connection->prepare($this->activeUserQuery);
    $activeUserStmt->bindValue(1, $username);

    return (bool) $activeUserStmt->executeQuery()->fetchOne();
  }

  public function getUserPermissionsById(int $userId): int {
    $getUserPermissionsQuery = $this->connection->prepare($this->getUserPermissionsByIdQuery);
    $getUserPermissionsQuery->bindValue(1, $userId);
    return (int)$getUserPermissionsQuery->executeQuery()->fetchOne();
  }

}
