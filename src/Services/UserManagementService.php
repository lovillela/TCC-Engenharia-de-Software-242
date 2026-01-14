<?php

namespace Lovillela\BlogApp\Services;

use Doctrine\DBAL\Connection;
use Lovillela\BlogApp\Config\UserPermissions\UserRole;
use Lovillela\BlogApp\Repositories\UserRepository;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Utils\PasswordHash;
use Lovillela\BlogApp\Config\UserPermissions;
use Lovillela\BlogApp\Services\AuthenticationControlService;

class UserManagementService{

  private UserRepository $userRepository;
  private PostManagementService $postService;
  private AuthenticationControlService $authenticationService;
  private Connection $connection;

  public function __construct(UserRepository $userRepository, AuthenticationControlService $authenticationService, 
                              PostManagementService $postService, Connection $connection){
    $this->userRepository = $userRepository;
    $this->authenticationService = $authenticationService;
    $this->postService = $postService;
    $this->connection = $connection;
  }
  
  public function create(string $username, string $password, string $email, int $role /**Account role to be created*/) {

    if($role === UserRole::Admin->value || $role === UserRole::Moderator->value){
      if (!($this->userAdminOrModCreationPrivilegeCheck())){
        RedirectService::redirectToHome();
      }
    }

    if($this->userRepository->exists($username)){
      return (array('Status' => 0, 'Message' => 'User already in use'));
    }

    if($this->userRepository->emailExists($email)){
      return (array('Status' => 0, 'Message' => 'Email already in use'));
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
      return (array('Status' => 0, 'Message' => 'User not created'));
    }

    return (array('Status' => 1, 'Message' => 'User created successfully'));
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

  private function getUserID($connection, $username)  {
      $sqlStatment_CheckUser = $connection->prepare($this->getUserID_Query);
      $sqlStatment_CheckUser->bindValue(1, $username);
      $result = $sqlStatment_CheckUser->executeQuery()->fetchAllAssociative();

      return($result[0]['id']);
  }

  public static function userAdminOrModCreationPrivilegeCheck(){
    
    if($_SESSION['role'] != 1){
      session_destroy();
      return false;
    }

    return true;
  }

  public static function adminPrivilegeCheck(){
    
    if($_SESSION['role'] != 1){
      session_destroy();
      return false;
    }

    return true;
  }

  private function checkUserSession($connection, $username) {

    $sqlStatment_CheckUser = $connection->prepare($this->checkUserCurrentSession);
    $sqlStatment_CheckUser->bindValue(1, $username);
    $result = $sqlStatment_CheckUser->executeQuery()->fetchAllAssociative();

    $userData = $result[0];
    
    if (!($userData['username'] === $_SESSION['user'])) {
      return false;
    }

    return true;
  }

  public function logout() {
    session_unset();
    session_destroy();
    RedirectService::redirectToHome();
    exit();
  }
}