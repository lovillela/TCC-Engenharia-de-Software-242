<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Utils\PasswordHash;
use Lovillela\BlogApp\Config\UserPermissions;

final class UserRepository
{
  private Connection $connection;
  private string $userCreatorQuery = 'INSERT INTO `users` (`username`, `email`, `password`, `permissions`, `isActive` ) 
                                      VALUES (?, ?, ?, ?, 1)';
  private string $activeUserQuery = "SELECT `username`, `password` , `permissions`, `isActive`, `id` 
                                        FROM `users` WHERE (isActive = ? AND username = ? AND permissions = ?)";
  private string $userExistsQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE `username` = ?) as usernameExists';
  private string $emailExistsQuery = 'SELECT EXISTS(SELECT 1 FROM `users` WHERE `email` = ?) as emailExists';
  private string $checkUserCurrentSession = 'SELECT * FROM `users` WHERE `username` = ?';
  private string $deleteUserQuery = 'DELETE FROM `users` WHERE `id` = ?';
  private string $getUserID_Query = 'SELECT `id` FROM `users` WHERE `username` = ?';
  private string $getUserPostsQuery = 'SELECT `id_post` FROM `post_users` WHERE `id_user` = ?';
  
  public function __construct(Connection $connection){
      $this->connection = $connection;
  }

  public function createUser(string $username, string $password, string $email, int $role /**Account role to be created*/) {
    
    //Checks if account being created is admin OR moderator
    /**
     * Checks if account being created is admin OR moderator
     * Verifica se a conta a ser criada é admin ou moderador
     */
    if (!(($role === 1 || $role === 2) && $this->userAdminOrModCreationPrivilegeCheck())) {
      RedirectService::redirectToHome();
    }
    
    $sqlStatment_UserCreation = $this->connection->prepare($this->userCreatorQuery);
    $sqlStatment_UserCreation->bindValue(1, $username);
    $sqlStatment_UserCreation->bindValue(2, $email);
    $sqlStatment_UserCreation->bindValue(3, $password);
    $sqlStatment_UserCreation->bindValue(4, $role);

    return (int) $sqlStatment_UserCreation->executeStatement();

  }

  public function delete(string $username) {
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

  public function userExists(string $username): bool {

    $queryUserExists = $this->connection->prepare($this->userExistsQuery);
    $queryUserExists->bindValue(1, $username);

    return (bool) $queryUserExists->executeQuery()->fetchOne();
  }

  public function emailExists(string $email): bool {

    $emailExistsQuery = $this->connection->prepare($this->userExistsQuery);
    $emailExistsQuery->bindValue(1, $email);

    return (bool) $emailExistsQuery->executeQuery()->fetchOne();
  }  
}
