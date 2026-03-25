<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Repositories\CommentRepository;
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

}