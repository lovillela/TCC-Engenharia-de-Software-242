<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Utils\PasswordHash;
use Lovillela\BlogApp\Services\RedirectService;

class UserManagementService{
  private string $userCreatorQuery = 'INSERT INTO `users` (`username`, `email`, `password`, `permissions`, `isActive` ) VALUES (?, ?, ?, ?, 1)';
  private string $userExistsQuery = 'SELECT `username` FROM `users` WHERE `username`= ?';
  private string $userExistsQueryV2 = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE `username` = ?) as usernameExists';
  private string $emailExistsQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE `email` = ?) as emailExists';
  private string $checkUserCurrentSession = 'SELECT * FROM `users` WHERE `username` = ?';
  private string $deleteUserQuery = 'DELETE FROM `users` WHERE `id` = ?';
  private string $getUserID_Query = 'SELECT `id` FROM `users` WHERE `username` = ?';
  private string $getUserPostsQuery = 'SELECT `id_post` FROM `post_users` WHERE `id_user` = ?';
  private string $deleteSlugMapQuery = 'DELETE FROM `slug_map` WHERE (`entity_id` = ? AND `entity_type` = ?)';
  private string $deletePostQuery = 'DELETE FROM `post` WHERE id = ?';
  private string $deletePost_UserQuery = 'DELETE FROM `post_users` WHERE id_user = ?';

  public function createUser($username, $password, $email, int $role /**Account role to be created*/) {
    
    //Checks if account being created is admin OR moderator
    if (!(($role === 1 || $role === 2) && $this->userAdminOrModCreationPrivilegeCheck())) {
      RedirectService::redirectToHome();
    }

    $password = PasswordHash::hashPassword($password);

    global $connection;
    //So the IDE can display all the methods, etc
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $connection;

    $userData = $this->checkIfUserExists($connection, $username);

    if(!empty($userData)){
      return (array('Status' => 0, 'Message' => 'User already exists'));
    }

    $sqlStatment_CheckEmailExistence = $connection->prepare($this->emailExistsQuery);
    $sqlStatment_CheckEmailExistence->bindValue(1, $email);
    $userData = $sqlStatment_CheckEmailExistence->executeQuery()->fetchOne();

    if(!empty($userData)){
      return (array('Status' => 0, 'Message' => 'Email already exists'));
    }

    $sqlStatment_UserCreation = $connection->prepare($this->userCreatorQuery);
    $sqlStatment_UserCreation->bindValue(1, $username);
    $sqlStatment_UserCreation->bindValue(2, $email);
    $sqlStatment_UserCreation->bindValue(3, $password);
    $sqlStatment_UserCreation->bindValue(4, $role);

    try {
      $queryResult = $sqlStatment_UserCreation->executeQuery();
    } catch (\Throwable $th) {
      //Register this to the log throw $th;
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

  private function checkIfUserExists($connection, $username) {

    $sqlStatment_CheckUserExistence = $connection->prepare($this->userExistsQueryV2);
    $sqlStatment_CheckUserExistence->bindValue(1, $username);

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