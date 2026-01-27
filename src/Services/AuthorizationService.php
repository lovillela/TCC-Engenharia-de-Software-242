<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Repositories\PostRepository;

final class AuthorizationService {

  private PostRepository $postRepository;

  public function __construct(PostRepository $postRepository) {
    $this->postRepository = $postRepository;
  }

  public function authorize(int $userId, int $contentId, int $permissions) : bool {
    return (bool)$this->postRepository->getOwnership($userId);
  }
}