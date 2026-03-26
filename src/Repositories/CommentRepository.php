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
  
  //Selects
  /**
   * WITH RECURSIVE -> para mysql 8.0+
   * seguindo a mesma lógica do user repo
   */
  private string $selectCommentWithChildrenQuery = '
    WITH RECURSIVE CommentWithChildren AS (
      SELECT `id`, 1 AS CommentDepth
      FROM `user_comment_post` 
      WHERE `id` = ?
      
      UNION ALL
      
      SELECT `user_comment_post`.`id`, CommentWithChildren.CommentDepth + 1
      FROM `user_comment_post`
      INNER JOIN CommentWithChildren ON `user_comment_post`.`parent` = CommentWithChildren.`id`
    )
  
    SELECT `id` FROM CommentWithChildren ORDER BY CommentDepth DESC';
  
  private string $selectCommentsQuery = 'SELECT `user_comment_post`.`id`, `user_comment_post`.`id_user`, 
                                              `user_comment_post`.`id_post`, `user_comment_post`.`content`, 
                                                `user_comment_post`.`parent`, `user_comment_post`.`created_at`, 
                                                `users`.`username` 
                                    FROM `user_comment_post`
                                    JOIN `users` ON `user_comment_post`.`id_user` = `users`.id
                                    WHERE `user_comment_post`.`id_post` = ? AND `user_comment_post`.`is_visible` = 1
                                    ORDER BY `user_comment_post`.`created_at` ASC';

  //Deletes
  /**
   * Lembre-se:
   * Usar em conjunto com selectCommentWithChildrenQuery -> IN (?)
   */
  private string $deleteCommentsInRangeQuery = 'DELETE FROM `user_comment_post` WHERE `id` IN (?)';
  public function __construct(Connection $connection, LoggerInterface $logger) {
    $this->connection = $connection;
    $this->logger = $logger;
  }

  public function create(int $userId, int $postId, string $content, ?int $parentId = null) {
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

  public function getPostComments(int $postId) : ?array {
    try {
      $selectCommentsStmt = $this->connection->prepare($this->selectCommentsQuery);
      $selectCommentsStmt->bindValue(1, $postId);
      return $selectCommentsStmt->executeQuery()->fetchAllAssociative();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao carregar comentários do post!', ['postId' => $postId, 'exception' => $th]);
        throw new Exception('Erro ao carregar comentários do post!');
    }
  }

  /**
   * Como no user repo
   * 
   */
  public function delete(int $commentId) {
    try {
      $commentWithChildren = $this->getCommentWithChildren($commentId);
      
      if (empty($commentWithChildren)) {
        return;
      }

      $this->connection->executeStatement($this->deleteCommentsInRangeQuery,
                                          [$commentWithChildren],
                                          [ArrayParameterType::INTEGER]);

    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar comentário e filhos!', 
                                ['commentId' => $commentId,'exception' => $th]);
        throw new Exception('Erro ao deletar comentário e filhos!'); 
    }
  }

  /**
   * Lê o comentário e respostas
   * @param int $userId
   * @throws Exception
   * @return array
   */
  private function getCommentWithChildren(int $commentId): ?array{
    try {
      $userCommentsWithChildrenStmt = $this->connection->prepare($this->selectCommentWithChildrenQuery);
      $userCommentsWithChildrenStmt->bindValue(1, $commentId);
      return $userCommentsWithChildrenStmt->executeQuery()->fetchFirstColumn();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao ler comentários do usuário!',
                                ['userId' => $commentId, 'exception' => $th]);
        throw new Exception('Erro ao ler comentários do usuário!');
    }
  }
}
