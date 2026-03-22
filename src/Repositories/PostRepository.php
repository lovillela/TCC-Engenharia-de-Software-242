<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ArrayParameterType;
use Exception;
use Psr\Log\LoggerInterface;
use Throwable;

class PostRepository{
  private Connection $connection;
  private LoggerInterface $logger;
  //Inserts
  private string $insertPostQuery = 'INSERT INTO `post` (`title`, `content`, `slug`) VALUES (?, ?, ?)';
  private string $insertPostUsersQuery = 'INSERT INTO `post_users` VALUES (?, ?) ';
  
  //Selects
  private string $selectAllPostsQuery = 'SELECT `title`, `content` FROM `post` LIMIT 50';
  private string $selectAllPostsForAdminQuery = 'SELECT `id`, `title` FROM `post`';
  private string $selectPostByID_Query = 'SELECT `id`, `title`, `content`, `slug` FROM `post` WHERE `id` = ?';
  private string $selectPostBySlugQuery = 'SELECT `title`, `content` FROM `post` WHERE `slug` = ?';
  private string $selectPostsByUserId = 'SELECT `id_post` FROM `post_users` WHERE `id_user` = ?';
  private string $selectPostIdsInRange = 'SELECT DISTINCT `id_post` FROM `post_users` WHERE `id_post` IN (?)';
  private string $selectOwnership = 'SELECT `id_user` FROM `post_users` WHERE `id_post` = ?';
  private string $selectOwnershipCount = 'SELECT COUNT(*) FROM `post_users` WHERE `id_post` = ?';

  //Joins
  private string $joinPost_PostUsers_ByUserId = 'SELECT `post`.`id`, `post`.`title` '
                                                .'FROM `post` '
                                                .'INNER JOIN `post_users` '
                                                .'ON `post`.`id` = `post_users`.`id_post` '
                                                .'WHERE `post_users`.`id_user` = ? ';

  //Updates
  private string $editPostQuery = 'UPDATE `post` SET `title` = ?, `content` = ?, `slug` = ? WHERE `id` = ?';

  //Deletes
  private string $deletePost = 'DELETE FROM `post` WHERE id = ?';
  private string $deletePostUsers = 'DELETE FROM `post_users` WHERE `id_user` = ?';
  private string $deleteAllUserReactions = 'DELETE FROM `user_reaction_post` WHERE `id_user` = ?';
  private string $deleteAllUserComments = 'DELETE FROM `user_comment_post` WHERE `id_user` = ?';
  private string $deleteAllPostCommentsInRange = 'DELETE FROM `user_comment_post` WHERE `id_post` IN (?)';
  private string $deleteAllPostReactionsInRange = 'DELETE FROM `user_reaction_post` WHERE `id_post` IN (?)';
  private string $deleteAllPostCategoriesInRange = 'DELETE FROM `post_category` WHERE `id_post` IN (?)';
  private string $deleteAllPostTagsInRange = 'DELETE FROM `post_tag` WHERE `id_post` IN (?)';
  private string $deleteAllPostsInRange = 'DELETE FROM `post` WHERE `id` IN (?)';
  private string $deletePostUserRelationship = 'DELETE FROM `post_users` WHERE `id_post` = ? AND `id_user` = ?';
  private string $deleteAllUsersFromPostUserRelationship = 'DELETE FROM `post_users` WHERE `id_post` = ?';

  public function __construct(Connection $connection, LoggerInterface $logger) {
    $this->connection = $connection;
    $this->logger = $logger;
  }

  public function save(string $title, string $content, string $slug, int $userID) : int {

    try {

      $sqlStatmentPostCreation = $this->connection->prepare($this->insertPostQuery);
      $sqlStatmentPostCreation->bindValue(1, $title);
      $sqlStatmentPostCreation->bindValue(2, $content);
      $sqlStatmentPostCreation->bindValue(3, $slug);
      $sqlStatmentPostCreation->executeStatement();

      $postID = (int) $this->connection->lastInsertId();

      $sqlStatmentPostUsers = $this->connection->prepare($this->insertPostUsersQuery);
      $sqlStatmentPostUsers->bindValue(1, $userID);
      $sqlStatmentPostUsers->bindValue(2, $postID);
      $sqlStatmentPostUsers->executeStatement();

      return $postID;

    } catch (Throwable $th) {
        $this->logger->error('Erro ao criar post!', ['exception' => $th]);
        throw new Exception('Erro ao criar post');
    }
  }

  public function delete(int $postId) {

    try {

      $deletePostStmt = $this->connection->prepare($this->deletePost);
      $deletePostStmt->bindValue(1, $postId);
      $deletePostStmt->executeStatement();

    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar post!', ['postId' => $postId, 'exception' => $th]);
        throw new Exception('Erro ao deletar post');
    }
  }

  public function update(string $title, string $content, string $slug, int $postId) {
    
    try {

      $editPostStmt = $this->connection->prepare($this->editPostQuery);
      $editPostStmt->bindValue(1, $title);
      $editPostStmt->bindValue(2, $content);
      $editPostStmt->bindValue(3, $slug);
      $editPostStmt->bindValue(4, $postId);
      $editPostStmt->executeStatement();
      
    } catch (Throwable $th) {
        $this->logger->error('Erro ao atualizar post!', ['id' => $postId, 'exception' => $th]);
        throw new Exception('Erro ao atualizar post');
    }
  }

  public function deletePostUserRelationship(int $postId, int $userId) {

    try {

      $deletePostUserRelationshipStmt = $this->connection->prepare($this->deletePostUserRelationship);
      $deletePostUserRelationshipStmt->bindValue(1, $postId);
      $deletePostUserRelationshipStmt->bindValue(2, $userId);
      $deletePostUserRelationshipStmt->executeStatement();

    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar relação entre post e usuário!', 
                              ['id' => $postId, 'userId' => $userId, 'exception' => $th]);
        throw new Exception('Erro ao deletar relação entre post e usuário!');
    }
  }

  public function deleteAllPostUserRelantionship(int $userId) {

    try {

      $deletePostUsersStmt = $this->connection->prepare(sql: $this->deletePostUsers);
      $deletePostUsersStmt->bindValue(1, $userId);
      $deletePostUsersStmt->executeStatement();

    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar as relações entre posts e usuário!', 
                                ['userId' => $userId, 'exception' => $th]);
          throw new Exception('Erro ao deletar as relações entre posts e usuário!');
    }

  }

  public function deleteAllUsersFromPost(int $postId) {
    try {
      $deleteAllUsersFromPostStmt = $this->connection->prepare($this->deleteAllUsersFromPostUserRelationship);
      $deleteAllUsersFromPostStmt->bindValue(1, $postId);
      $deleteAllUsersFromPostStmt->executeStatement();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar autores do post!', ['postId' => $postId, 'exception' => $th]);
        throw new Exception('Erro ao deletar autores do post!');
    }
  }


  public function deleteAllUserReactionsByUserId(int $userId){

    try {

      $deleteAllUserReactionsStmt = $this->connection->prepare($this->deleteAllUserReactions);
      $deleteAllUserReactionsStmt->bindValue(1, $userId);
      $deleteAllUserReactionsStmt->executeStatement();

    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar as reações do usuário!', 
                                ['userId' => $userId, 'exception' => $th]);
          throw new Exception('Erro ao deletar as reações do usuário!');      
    }
  }

  public function deleteAllUserCommentsByUserId(int $userId) {

    try {

      $deleteAllUserCommentsStmt = $this->connection->prepare($this->deleteAllUserComments);
      $deleteAllUserCommentsStmt->bindValue(1, $userId);
      $deleteAllUserCommentsStmt->executeStatement();

    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar os comentários do usuário!', 
                                ['userId' => $userId, 'exception' => $th]);
          throw new Exception('Erro ao deletar os comentários do usuário!');      
    }
  }

  public function deletePostCommentsInRange(array $postIds) {

    try {
      
      $this->connection->executeStatement($this->deleteAllPostCommentsInRange,
                                                [$postIds],
                                                [ArrayParameterType::INTEGER]);
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar os comentários dos posts!', 
                                ['postIds' => $postIds, 'exception' => $th]);
          throw new Exception('Erro ao deletar os comentários dos posts!');   
    }
  }

  public function deletePostReactionsInRange(array $postIds) {

    try {
      $this->connection->executeStatement($this->deleteAllPostReactionsInRange,
                                          [$postIds],
                                          [ArrayParameterType::INTEGER]);
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar reações dos posts!', 
                                ['postIds' => $postIds, 'exception' => $th]);
          throw new Exception('Erro ao deletar reações dos posts!');   
    }
  }

  public function deletePostCategoriesInRange(array $postIds) {
    try {
      $this->connection->executeStatement($this->deleteAllPostCategoriesInRange,
                                            [$postIds],
                                            [ArrayParameterType::INTEGER]);
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar as categorias dos posts!', 
                                ['postIds' => $postIds, 'exception' => $th]);
          throw new Exception('Erro ao deletar as categorias dos posts!');      
    }    
  }

  public function deletePostTagsInRange(array $postIds) {

    try {

      $this->connection->executeStatement($this->deleteAllPostTagsInRange,
                                            [$postIds],
                                            [ArrayParameterType::INTEGER]);	      
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar tags dos posts!', 
                                ['postIds' => $postIds, 'exception' => $th]);
          throw new Exception('Erro ao deletar tags dos posts!');     
    }  
    
  }

  public function deleteAllPostsInRange(array $postIds){

    try {
      $this->connection->executeStatement($this->deleteAllPostsInRange,
                                              [$postIds],
                                              [ArrayParameterType::INTEGER]);
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar posts em lote!', 
                                ['postIds' => $postIds, 'exception' => $th]);
          throw new Exception('Erro ao deletar posts em lote!');     
    }  
  }

  public function getAllPosts(): array{
    
    try {

      $posts = $this->connection->executeQuery($this->selectAllPostsQuery);
      $posts = $posts->fetchAllAssociative();

      return $posts;
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler todos os posts', 
                                ['exception' => $th]);
          throw new Exception('Erro ao ler todos os posts!');     
    }
    
  }

  public function getPostBySlug(string $slug): array|null{
    
    try {
      $getPost = $this->connection->prepare($this->selectPostBySlugQuery);
      $getPost->bindValue(1, $slug);

      return $getPost->executeQuery()->fetchAssociative() ?: null;

    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler post por slug!', 
                                ['slug' => $slug, 'exception' => $th]);
          throw new Exception('Erro ao ler post por slug!');     
    }    
  }

  public function getPostByID(int $id): array|null {
    try {
      $getPost = $this->connection->prepare($this->selectPostByID_Query);
      $getPost->bindValue(1, $id);

      return $getPost->executeQuery()->fetchAssociative() ?: null;
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler post por id!', 
                                ['id' => $id, 'exception' => $th]);
          throw new Exception('Erro ao ler post por id!');     
    }

  }

  public function getUsersPostsByUserId(int $userId): array {
    try {
      $selectPostsByUserIdStmt = $this->connection->prepare($this->selectPostsByUserId);
      $selectPostsByUserIdStmt->bindValue(1, $userId);

      return $selectPostsByUserIdStmt->executeQuery()->fetchAllAssociative() ?: null;
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler posts do usuário!', 
                                ['userId' => $userId, 'exception' => $th]);
          throw new Exception('Erro ao ler posts do usuário!');     
    }    
  }

  public function getAllPostsIdsAndTitlesForAdmin() {
    try {
      $selectAllPostsIdsTitles = $this->connection->prepare($this->selectAllPostsForAdminQuery);

      return $selectAllPostsIdsTitles->executeQuery()->fetchAllAssociative();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler ids e títulos de posts de todos os usuários!', 
                                ['exception' => $th]);
          throw new Exception('Erro ao ler ids e títulos de posts de todos os usuários!');
    }
  }

  public function getAllPostsIdsAndTitlesByUserId(int $userId) {
    try {
      $joinPost_PostUsers_ByUserIdStmt = $this->connection->prepare($this->joinPost_PostUsers_ByUserId);
      $joinPost_PostUsers_ByUserIdStmt->bindValue(1, $userId);

      return $joinPost_PostUsers_ByUserIdStmt->executeQuery()->fetchAllAssociative() ?: null;
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler ids e títulos de posts do usuário!', 
                                ['userId' => $userId, 'exception' => $th]);
          throw new Exception('Erro ao ler ids e títulos de posts do usuário!'); 
    }
  }

  public function getPostIdsInRange(array $postIds): array {
    try {
      return $this->connection->executeQuery($this->selectPostIdsInRange, 
                                          [$postIds], 
                                          [ArrayParameterType::INTEGER])->fetchAllAssociative();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler posts em lote!', 
                                ['postIds' => $postIds, 'exception' => $th]);
          throw new Exception('Erro ao ler posts em lote!');     
    }    
   
  }

  public function getOwnershipCount(int $postId): int {

    try {
      $getOwnershipCountQuery = $this->connection->prepare($this->selectOwnershipCount);
      $getOwnershipCountQuery->bindValue(1, $postId);

      return (int)$getOwnershipCountQuery->executeQuery()->fetchOne();

    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler quantidade de autores do post!', 
                                ['postId' => $postId, 'exception' => $th]);
          throw new Exception('Erro ao ler quantidade de autores do post!');     
    }  

  }
  public function getOwnership(int $postId): ?int {

    try {
      $selectOwnershipStmt = $this->connection->prepare($this->selectOwnership);
      $selectOwnershipStmt->bindValue(1, $postId);
      $ownerId = $selectOwnershipStmt->executeQuery()->fetchOne();
      
      return ($ownerId !== false ) ? (int)$ownerId : null;

    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler autoria do post!', 
                                ['postId' => $postId, 'exception' => $th]);
          throw new Exception('Erro ao ler autoria do post!');     
    }

  }
  
}