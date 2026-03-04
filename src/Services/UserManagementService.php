<?php

namespace Lovillela\BlogApp\Services;

use Doctrine\DBAL\Connection;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Repositories\UserRepository;
use Lovillela\BlogApp\Utils\PasswordHash;
use Lovillela\BlogApp\Services\InputSanitizationService;
use Psr\Log\LoggerInterface;

class UserManagementService{

  private UserRepository $userRepository;
  private PostManagementService $postService;
  private InputSanitizationService $sanitizationService;
  private Connection $connection;
  private LoggerInterface $logger;

  public function __construct(UserRepository $userRepository,
                              PostManagementService $postService, 
                              InputSanitizationService $sanitizationService,
                              Connection $connection,
                              LoggerInterface $logger){
    $this->userRepository = $userRepository;
    $this->postService = $postService;
    $this->sanitizationService = $sanitizationService;
    $this->connection = $connection;
    $this->logger = $logger;
  }
  
  public function create(string $username, string $password, string $email, int $role /**Account role to be created*/) {

    $username = $this->sanitizationService->usernameSanitize($username);

    if($this->userRepository->exists($username)){
      return array('Status' => 0, 'Message' => 'User already in use');
    }

    if($this->userRepository->emailExists($email)){
      return array('Status' => 0, 'Message' => 'Email already in use');
    }

    try {

      $password = PasswordHash::hashPassword($password);
      $this->connection->beginTransaction();

      if (!($this->userRepository->create($username, $password, $email, $role))) {
        $this->connection->rollBack();
        return (array('Status' => 0, 'Message' => 'User not created'));
      }

      $this->connection->commit();
      
    } catch (\Throwable $th) {
      $this->connection->rollBack();
      return array('Status' => 0, 'Message' => 'User not created');
    }

    return array('Status' => 1, 'Message' => 'User created successfully');
  }

  public function deleteByUserName(string $username) {
    
    $userId = $this->userRepository->findIdByUsername($username);

    if (!$userId) {
      return ['Status' => 0, 'Message' => 'User not found'];
    }
  
    try {
      $this->connection->beginTransaction();
      $this->postService->deleteAllUserPostsByUserId((int)$userId);
      $this->userRepository->delete((int)$userId);
      $this->connection->commit();
    } catch (\Throwable $th) {
      $this->connection->rollBack();
      return ['Status' => 0, 'Message' => 'Error deleting user'];
      //throw $th;
    }
    
    return ['Status' => 1, 'Message' => 'User deleted successfully'];
  }

  public function findByEmail(string $email): ?array {
    return $this->userRepository->findByEmail($email);
  }

  public function getUserPermissionsById(int $userId) {
    return $this->userRepository->getUserPermissionsById($userId);
  }
  
}