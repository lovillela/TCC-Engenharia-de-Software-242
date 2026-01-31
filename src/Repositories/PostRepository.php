<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;
use Throwable;

class PostRepository{
  private Connection $connection;
  //Inserts
  private string $insertPostQuery = 'INSERT INTO `post` (`title`, `content`, `slug`) VALUES (?, ?, ?)';
  private string $insertPostUsersQuery = 'INSERT INTO `post_users` VALUES (?, ?) ';
  
  //Selects
  private string $selectAllPostsQuery = 'SELECT `title`, `content` FROM `post` LIMIT 50';
  private string $selectPostByID_Query = 'SELECT `title`, `content` FROM `post` WHERE `id` = ?';
  private string $selectPostBySlugQuery = 'SELECT `title`, `content` FROM `post` WHERE `slug` = ?';
  private string $selectPostsByUserId = 'SELECT `id_post` FROM `post_users` WHERE `id_user` = ?';
  private string $selectPostIdsInRange = 'SELECT DISTINCT `id_post` FROM `post_users` WHERE `id_post` IN (?)';
  private string $selectOwnership = 'SELECT `id_user` FROM `post_users` WHERE `id_post` = ?';
  private string $selectOwnershipCount = 'SELECT COUNT (*) FROM `post_users` WHERE `id_post` = ?';

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

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function save(string $title, string $content, string $slug, int $userID) : int {

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
  }

  public function delete(int $postId): bool {
    $deletePostStmt = $this->connection->prepare($this->deletePost);
    $deletePostStmt->bindValue(1, $postId);

    return (bool) $deletePostStmt->executeStatement();
  }

  public function deletePostUserRelationship(int $postId, int $userId) : bool {
    $deletePostUserRelationshipStmt = $this->connection->prepare($this->deletePostUserRelationship);
    $deletePostUserRelationshipStmt->bindValue(1, $postId);
    $deletePostUserRelationshipStmt->bindValue(2, $userId);

    return (bool)$deletePostUserRelationshipStmt->executeStatement();
  }

  public function deleteAllPostUserRelantionship(int $userId): bool {
    $deletePostUsersStmt = $this->connection->prepare(sql: $this->deletePostUsers);
    $deletePostUsersStmt->bindValue(1, $userId);

    return (bool) $deletePostUsersStmt->executeStatement();
  }

  public function deleteAllUserReactionsByUserId(int $userId): bool {
    $deleteAllUserReactionsStmt = $this->connection->prepare($this->deleteAllUserReactions);
    $deleteAllUserReactionsStmt->bindValue(1, $userId);

    return (bool)$deleteAllUserReactionsStmt->executeStatement();
  }

  public function deleteAllUserCommentsByUserId(int $userId): bool {
    $deleteAllUserCommentsStmt = $this->connection->prepare($this->deleteAllUserComments);
    $deleteAllUserCommentsStmt->bindValue(1, $userId);

    return (bool)$deleteAllUserCommentsStmt->executeStatement();
  }

  public function deletePostCommentsInRange(array $postIds): bool {
    return (bool)$this->connection->executeStatement($this->deleteAllPostCommentsInRange,
                                                [$postIds],
                                                [$this->connection::PARAM_INT_ARRAY]);
  }

  public function deletePostReactionsInRange(array $postIds) : bool {
    return (bool)$this->connection->executeStatement($this->deleteAllPostReactionsInRange,
                                                      [$postIds],
                                                      [$this->connection::PARAM_INT_ARRAY]);
  }

  public function deletePostCategoriesInRange(array $postIds) : bool {
    return (bool)$this->connection->executeStatement($this->deleteAllPostCategoriesInRange,
                                                      [$postIds],
                                                      [$this->connection::PARAM_INT_ARRAY]);
  }

  public function deletePostTagsInRange(array $postIds) : bool {
    return (bool)$this->connection->executeStatement($this->deleteAllPostTagsInRange,
                                                      [$postIds],
                                                      [$this->connection::PARAM_INT_ARRAY]);
  }

  public function deleteAllPostsInRange(array $postIds) : bool {
    return (bool)$this->connection->executeStatement($this->deleteAllPostsInRange,
                                                      [$postIds],
                                                      [$this->connection::PARAM_INT_ARRAY]);
  }

  public function getAllPosts(): array{

    $posts = $this->connection->executeQuery($this->selectAllPostsQuery);
    $posts = $posts->fetchAllAssociative();

    return $posts;
  }

  public function getPostBySlug(string $slug): array|null{
    
    $getPost = $this->connection->prepare($this->selectPostBySlugQuery);
    $getPost->bindValue(1, $slug);

    return $getPost->executeQuery()->fetchAssociative() ?: null;
  }

  public function getPostByID(int $id): array|null {

    $getPost = $this->connection->prepare($this->selectPostByID_Query);
    $getPost->bindValue(1, $id);

    return $getPost->executeQuery()->fetchAssociative() ?: null;
  }

  public function getUsersPostsByUserId(int $userId): array {
    $selectPostsByUserIdStmt = $this->connection->prepare($this->selectPostsByUserId);
    $selectPostsByUserIdStmt->bindValue(1, $userId);

    return $selectPostsByUserIdStmt->executeQuery()->fetchAllAssociative() ?: null;
  }

  public function getPostIdsInRange(array $postIds): array {
    return $this->connection->executeQuery($this->selectPostIdsInRange, 
                                          [$postIds], 
                                          [$this->connection::PARAM_INT_ARRAY])->fetchAllAssociative();
  }

  public function getOwnershipCount(int $postId): int {
    $getOwnershipCountQuery = $this->connection->prepare($this->selectOwnershipCount);
    $getOwnershipCountQuery->bindValue(1, $postId);
    return (int)$getOwnershipCountQuery->executeQuery()->fetchOne();
  }
  public function getOwnership(int $postId): ?int {
    $selectOwnershipStmt = $this->connection->prepare($this->selectOwnership);
    $selectOwnershipStmt->bindValue(1, $postId);
    $ownerId = $selectOwnershipStmt->executeQuery()->fetchOne();
    
    return ($ownerId !== false ) ? (int)$ownerId : null;
  }

  private static function databaseExceptionHandler(Throwable $e)   {
    $errors= [
      \Doctrine\DBAL\Exception\ConnectionException::class => 'Connection Error!',
      \Doctrine\DBAL\Exception\UniqueConstraintViolationException::class => 'Slug Already exists. Choose another title.',
      \Doctrine\DBAL\Exception\SyntaxErrorException::class => 'Syntax Error. The developer messed up!'
    ];

    $message = $errors[get_class(object: $e)] ?? 'General Error';

    return array('Status' => 0, 'Message' => $message);
  }
  
}