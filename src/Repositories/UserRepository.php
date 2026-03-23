<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Throwable;
use Exception;

final class UserRepository
{
  private Connection $connection;
  private LoggerInterface $logger;

  //Deletes
  private string $userCreatorQuery = 'INSERT INTO `users` (`username`, `email`, `password`, `permissions`, `isActive` ) 
                                      VALUES (?, ?, ?, ?, 1)';

  //Selects
  private string $activeUserQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE (`username` = ?) AND (isActive = 1)) as isActive';
  private string $userExistsQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE `username` = ?) as usernameExists';
  private string $emailExistsQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE `email` = ?) as emailExists';
  private string $getUserInfoQueryEmail = 'SELECT `id`, `username`, `email`, `password` ,`isActive`, `permissions` FROM 
                                          `users` WHERE (`email` = ? AND isActive = 1)';
  private string $getUserInfoQueryUsername = 'SELECT `id`, `username`, `email`, `password`, `isActive` FROM 
                                              `users` WHERE (`username` = ? AND isActive = 1)';
  private string $getUserIdByUsernameQuery = 'SELECT `id` FROM `users` WHERE `username` = ?';
  private string $getUserPermissionsByIdQuery = 'SELECT `permissions` FROM `users` WHERE `id` = ?';
  private string $selectAllUsers = 'SELECT `id`, `username`, `email`, `permissions` FROM `users`';
  //Deletes
  private string $deleteUserQuery = 'DELETE FROM `users` WHERE `id` = ?';
  
  public function __construct(Connection $connection, LoggerInterface $logger){
      $this->connection = $connection;
      $this->logger = $logger;
  }

  public function create(string $username, string $password, string $email, int $role /**Account role to be created*/) {
    
    try {
      $sqlStatment_UserCreation = $this->connection->prepare($this->userCreatorQuery);
      $sqlStatment_UserCreation->bindValue(1, $username);
      $sqlStatment_UserCreation->bindValue(2, $email);
      $sqlStatment_UserCreation->bindValue(3, $password);
      $sqlStatment_UserCreation->bindValue(4, $role);

      $sqlStatment_UserCreation->executeStatement();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao criar usário!', 
                                ['username' => $username, 'email' => $email, 'role' => $role,'exception' => $th]);
          throw new Exception('Erro ao criar usário!');     
    }    
  }

  public function delete(int $userId) {

    try {
      
      $deleteUserStmt = $this->connection->prepare($this->deleteUserQuery);
      $deleteUserStmt->bindValue(1, $userId);

      $deleteUserStmt->executeStatement();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar usário!', 
                                ['userId' => $userId,'exception' => $th]);
          throw new Exception('Erro ao deletar usário!');     
    }
  }

  public function exists(string $username): bool {

    try {
      $queryUserExists = $this->connection->prepare($this->userExistsQuery);
      $queryUserExists->bindValue(1, $username);

      return (bool) $queryUserExists->executeQuery()->fetchOne();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao verificar se usuário já está em uso!', 
                                ['username' => $username, 'exception' => $th]);
          throw new Exception('Erro ao verificar se usuário já está em uso!');
    }    
  }

  public function emailExists(string $email): bool {

    try {
      $emailExistsStmt = $this->connection->prepare($this->emailExistsQuery);
      $emailExistsStmt->bindValue(1, $email);

      return (bool) $emailExistsStmt->executeQuery()->fetchOne();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao verificar se email já está em uso!', 
                                ['email' => $email, 'exception' => $th]);
          throw new Exception('Erro ao verificar se email já está em uso!');     
    }
  }

  public function findByUsername(string $username) : ?array {

    try {
      $getUserInfoStmt = $this->connection->prepare($this->getUserInfoQueryUsername);
      $getUserInfoStmt->bindValue(1, $username);

      return $getUserInfoStmt->executeQuery()->fetchAssociative() ?: null;    
    } catch (Throwable $th) {
        $this->logger->error('Erro ao procurar o usário!', 
                                ['username' => $username, 'exception' => $th]);
          throw new Exception('Erro ao procurar o usário!');     
    }    
  }

  public function findByEmail(string $email) : ?array {

    try {
      $getUserInfoStmt = $this->connection->prepare($this->getUserInfoQueryEmail);
      $getUserInfoStmt->bindValue(1, $email);

      return $getUserInfoStmt->executeQuery()->fetchAssociative() ?: null;
    } catch (Throwable $th) {
        $this->logger->error('Erro ao procurar o usário por email!', 
                                ['exception' => $th]);
          throw new Exception('Erro ao procurar o usário por email!');     
    }  
  }

  public function findIdByUsername(string $username) {

    try {
      $getUserIdStmt = $this->connection->prepare($this->getUserIdByUsernameQuery);
      $getUserIdStmt->bindValue(1, $username);

      return $getUserIdStmt->executeQuery()->fetchOne() ?: false;
    } catch (Throwable $th) {
        $this->logger->error('Erro ao procurar o usário!', 
                                ['username' => $username, 'exception' => $th]);
          throw new Exception('Erro ao procurar o usário!');     
    }    
  }
  
  public function isActive(string $username) : bool {

    try {
      $activeUserStmt = $this->connection->prepare($this->activeUserQuery);
      $activeUserStmt->bindValue(1, $username);

      return (bool) $activeUserStmt->executeQuery()->fetchOne();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao verificar se o usuário está ativo!',
                                ['username' => $username, 'exception' => $th]);
          throw new Exception('Erro ao verificar se o usuário está ativo!');
    }  

  }

  public function getUserPermissionsById(int $userId): int {

    try {
      $getUserPermissionsQuery = $this->connection->prepare($this->getUserPermissionsByIdQuery);
      $getUserPermissionsQuery->bindValue(1, $userId);
      return (int)$getUserPermissionsQuery->executeQuery()->fetchOne();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler premissões do usuário!', 
                                ['exception' => $th]);
          throw new Exception('Erro ao ler premissões do usuário!');  
    }
  }

  public function getAllUsers() : ?array {
    try {
      return $this->connection->executeQuery($this->selectAllUsers)->fetchAllAssociative();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler todos os usuários!', 
                                ['exception' => $th]);
          throw new Exception('Erro ao ler todos os usuários!'); 
    }
  }
}
