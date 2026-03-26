<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Models\Comments\CommentData;
use Lovillela\BlogApp\Repositories\CommentRepository;
use Lovillela\BlogApp\Models\Comments;
use Psr\Log\LoggerInterface;
use Throwable;
use Exception;

final class CommentService {

  private CommentRepository $commentRepository;
  private LoggerInterface $logger;

  public function __construct(CommentRepository $commentRepository, LoggerInterface $logger) {
    $this->commentRepository = $commentRepository;
    $this->logger = $logger;
  }

  public function create(int $userId, int $postId, string $content, ?int $parentId = null) {

    try {
      $this->commentRepository->create($userId, $postId, $content, $parentId);
    } catch (Throwable $th) {
        $this->logger->error('Erro ao salvar comentário no post', ['postId' => $postId, 'exception' => $th->getMessage()]);
        throw new Exception('Erro ao salvar comentário no post');
    }

  }

  public function delete(int $commentId) {

    try {
      $this->commentRepository->delete($commentId);
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar comentário', ['commentId' => $commentId, 'exception' => $th->getMessage()]);
        throw new Exception('Erro ao deletar comentário');
    }
  }

  public function getPostComments(int $postId) : ?array {
    $postComments = $this->commentRepository->getPostComments($postId);
    $postCommentsData = [];

    if (!isset($postComments)) {
      return [];
    }

    foreach ($postComments as $postComment) {

      $postCommentModel = new CommentData(
                                $postComment['id'],
                                $postComment['parent'] !== null ? $postComment['parent'] : null,
                                $postComment['content'],
                                $postComment['created_at'],
                                $postComment['username'],
                              );

      array_push($postCommentsData, $postCommentModel);
    }

    return $postCommentsData;
  }

}