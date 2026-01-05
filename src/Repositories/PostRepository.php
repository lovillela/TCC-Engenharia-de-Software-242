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