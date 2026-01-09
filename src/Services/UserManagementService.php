<?php

namespace Lovillela\BlogApp\Services;

use Doctrine\DBAL\Connection;
use Lovillela\BlogApp\Repositories\UserRepository;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Utils\PasswordHash;

class UserManagementService{

  private UserRepository $userRepository;
  private Connection $connection;

  public function __construct(UserRepository $userRepository, Connection $connection){
    $this->userRepository = $userRepository;
    $this->connection = $connection;
  }

  public function create($username, $password, $email, int $role /**Account role to be created*/) {
    
    $password = PasswordHash::hashPassword($password);

    //Checks if account being created is admin OR moderator
    if (!(($role === 1 || $role === 2) && $this->userAdminOrModCreationPrivilegeCheck())) {
      RedirectService::redirectToHome();
    }

    if($this->userRepository->userExists($username)){
      return (array('Status' => 0, 'Message' => 'User already in use'));
    }

    if($this->userRepository->emailExists($email)){
      return (array('Status' => 0, 'Message' => 'Email already in use'));
    }

    try {
      $this->connection->beginTransaction();
      $this->userRepository->create($username, $password, $email, $role);
      $this->connection->commit();
    } catch (\Throwable $th) {
      $this->connection->rollBack();
      return (array('Status' => 0, 'Message' => 'User not created'));
    }

    return (array('Status' => 1, 'Message' => 'User created successfully'));
  }

  public function delete($username) {
    global $connection;
    //So the IDE can display all the methods, etc
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $connection;

    $userData = $this->checkIfUserExists($connection, $username);

    if(empty($userData)){
      return (array('Status' => 0, 'Message' => 'User does not exist'));
    }

    if (!($this->checkUserSession($connection, $username)) && !($this->adminPrivilegeCheck())) {
      return (array('Status' => 0, 'Message' => 'Operation not authorized!'));
    }else{
      //Since the user is requesting its account deletion or is an admin
      //get user ID

      $userID = $this->getUserID($connection, $username);

      $sqlStatment_GetPosts = $connection->prepare($this->getUserPostsQuery);
      $sqlStatment_GetPosts->bindValue(1, $userID);
      $userPostIDs = $sqlStatment_GetPosts->executeQuery()->fetchAllAssociative();
      
      try {
        $connection->beginTransaction();

        foreach ($userPostIDs as $entityID) {
          $sqlStatment_DeleteSlugMap = $connection->prepare($this->deleteSlugMapQuery);
          $sqlStatment_DeleteSlugMap->bindValue(1, $entityID['id_post']);
          $sqlStatment_DeleteSlugMap->bindValue(2, 'post');
          $sqlStatment_DeleteSlugMap->executeQuery();
        }

        //post <- -> user id relationship
        $sqlStatment_DeletePost_User = $connection->prepare($this->deletePost_UserQuery);
        $sqlStatment_DeletePost_User->bindValue(1, $userID);
        $sqlStatment_DeletePost_User->executeQuery();

        foreach ($userPostIDs as $entityID) {
          $sqlStatmentDeletePost = $connection->prepare($this->deletePostQuery);
          $sqlStatmentDeletePost->bindValue(1, $entityID['id_post']);
          $sqlStatmentDeletePost->executeQuery();
        }

          $sqlStatment_DeleteUser = $connection->prepare($this->deleteUserQuery);
          $sqlStatment_DeleteUser->bindValue(1, $userID);
          $sqlStatment_DeleteUser->executeQuery();

          $connection->commit();

        } catch (\Throwable $th) {
          $connection->rollBack();
          throw $th;
        }

      return (array('Status' => 1, 'Message' => 'User destroyed!'));
    }
  }

  private function getUserID($connection, $username)  {
      $sqlStatment_CheckUser = $connection->prepare($this->getUserID_Query);
      $sqlStatment_CheckUser->bindValue(1, $username);
      $result = $sqlStatment_CheckUser->executeQuery()->fetchAllAssociative();

      return($result[0]['id']);
  }

  public function userExists(string $username) {

    $this->userRepository->userExists($username);

    return $sqlStatment_CheckUserExistence->executeQuery()->fetchOne();
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