<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Throwable;
use Exception;
use Doctrine\DBAL\ArrayParameterType;

final class CommentRepository {
  private Connection $connection;
  private LoggerInterface $logger;

  //Inserts
  private string $insertCommentQuery = 'INSERT INTO `user_comment_post` 
                                        (`id_user`, `id_post`, `content`, `parent`, `created_at`, `is_visible`) 
                                        VALUES 
                                        (?, ?, ?, ?, ?, ?)';

  public function __construct(Connection $connection, LoggerInterface $logger) {
    $this->connection = $connection;
    $this->logger = $logger;
  }

  public function save(int $userId, int $postId, string $content, ?int $parentId = null) {
    try {
      $insertCommentStmt = $this->connection->prepare($this->insertCommentQuery);
      $insertCommentStmt->bindValue(1, $userId);
      $insertCommentStmt->bindValue(2, $postId);
      $insertCommentStmt->bindValue(3, $content);
      $insertCommentStmt->bindValue(4, $parentId);
      $insertCommentStmt->bindValue(5, date("Y-m-d H:i:s"));
      $insertCommentStmt->bindValue(6, 1);

      $insertCommentStmt->executeQuery();

    } catch (Throwable $th) {
        $this->logger->error('Erro ao salvar comentário!', 
                            ['userId' => $userId, 
                            'postId' => $postId,
                            'exception' => $th]);
        throw new Exception('Erro ao salvar comentário!');
    }
  }

  public function getPostComments(int $postId) {
    
  }

  public function deleteComment(int $commentId) {
    
  }
}
