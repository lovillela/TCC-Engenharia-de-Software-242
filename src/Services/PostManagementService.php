<?php 

namespace Lovillela\BlogApp\Services;

use Doctrine\DBAL\Connection;
use Lovillela\BlogApp\Repositories\PostRepository;
use Throwable;
use Lovillela\BlogApp\Services\SlugService;
use Lovillela\BlogApp\Services\InputSanitizationService;

class PostManagementService {

  private PostRepository $postRepository;
  private SlugService $slugService;
  private InputSanitizationService $sanitizationService;
  private Connection $connection;
  private const ENTITY = 'post';
  private const BATCH_SIZE = 1000;

  public function __construct(PostRepository $postRepository, SlugService $slugService, 
                              InputSanitizationService $sanitizationService, Connection $connection){
    
    $this->postRepository = $postRepository;
    $this->slugService = $slugService;
    $this->sanitizationService = $sanitizationService;
    $this->connection = $connection;
    
    }

  public function create(string $title, string $text, int $userID): array{
    
    /**
     * Checar autorização e autenticação antes
     * Check authorization and authentication prior
     */
    
    $title = $this->sanitizationService->postTitleSanitize($title);
    $text = $this->sanitizationService->postContentSanitize($text);

    try {
      $this->connection->beginTransaction();

      $slugURL = $this->slugService->create($this::ENTITY, $title);

      $postID = $this->postRepository->save($title, $text, 
                              $slugURL, $userID);
      
      $this->slugService->save($this::ENTITY, $slugURL, $postID);

      $this->connection->commit();

      return (array('Status' => 1, 'Message' => 'Post Created Succesfully!'));

    }catch(Throwable $e){
        $this->connection->rollBack();
        return $this->databaseExceptionHandler($e);
    }
  }

  public function delete(int $postId, int $userId) {
    try {
      
      $this->connection->beginTransaction();
      $this->postRepository->deletePostUserRelationship($postId, $userId);
      $ownershipCount = $this->postRepository->getOwnershipCount($postId);

      if ($ownershipCount === 0) {
        $this->postRepository->deletePostTagsInRange(array($postId));
        $this->postRepository->deletePostReactionsInRange(array($postId));
        $this->postRepository->deletePostCommentsInRange(array($postId));
        $this->postRepository->deletePostCategoriesInRange(array($postId));
        $this->slugService->deleteInRange(array($postId), $this::ENTITY);
        $this->postRepository->delete($postId);
      }

      $this->connection->commit();
      
    } catch (\Throwable $th) {
      $this->connection->rollBack();
      //throw $th;
    }
  }

  public function update(string $title, string $text, string $slug, int $postId) {
    
    try {
      
      $this->connection->beginTransaction();
      
      $title = $this->sanitizationService->postTitleSanitize($title);
      $text = $this->sanitizationService->postContentSanitize($text);
      $slug = $this->sanitizationService->slugSanitize($slug);
      
      $postId = $this->sanitizationService->idSanitize($postId);

      $this->postRepository->update($title, $text, $slug, $postId);

      $this->connection->commit();
      
    } catch (\Throwable $th) {
      $this->connection->rollBack();
    }
  }

  public function deleteAllUserPostsByUserId(int $userId) {
    
    try {
      $this->connection->beginTransaction();

      $this->postRepository->deleteAllUserReactionsByUserId($userId);
      $this->postRepository->deleteAllUserCommentsByUserId($userId);
      $userPosts = array_column($this->postRepository->getUsersPostsByUserId($userId), 'id_post');

      if (!empty($userPosts)) {
        $this->postRepository->deleteAllPostUserRelantionship($userId);
        $postIdsChunk = array_chunk($userPosts, $this::BATCH_SIZE);
        
        foreach ($postIdsChunk as $postIdsBatch) {
          $otherUsersPosts = array_column($this->postRepository->getPostIdsInRange($postIdsBatch), 'id_post');
          /**
           * postIdsBatch - otherUsersPosts
           * Necessary array_values to reset array indexes
           * É necessário utilizar array_values para reiniciar os índicies do array
           */
          $userPostsToDelete = array_values(array_diff($postIdsBatch, $otherUsersPosts)) ;

          /**
           * Now, post_tag, post_category, user_comment_post and user_reaction_post 
           * entries related to $userPostsToDelete must be deleted
           * Agora as entradas em post_tag, post_category, user_comment_post e user_reaction_post,
           * relacionadas a $userPostsToDelete devem ser deletadas
           */
          if (!empty($userPostsToDelete)) {
            $this->postRepository->deletePostCommentsInRange($userPostsToDelete);
            $this->postRepository->deletePostReactionsInRange($userPostsToDelete);
            $this->postRepository->deletePostCategoriesInRange($userPostsToDelete);
            $this->postRepository->deletePostTagsInRange($userPostsToDelete);
            $this->slugService->deleteInRange($userPostsToDelete, $this::ENTITY);
            $this->postRepository->deleteAllPostsInRange($userPostsToDelete);
          }
        }

      }

      $this->connection->commit();
    } catch (\Throwable $th) {
      //throw $th;
      $this->connection->rollBack();
    }
  }

  public function getAllPosts(): array{
    return $this->postRepository->getAllPosts();
  }

  public function getPostBySlug(string $slug): ?array {

    $post = $this->postRepository->getPostBySlug($slug);

    if (!isset($post)) {
      return null;
    }

    $post = $this->sanitizationService->displayPostSanitize($post);

    return $post;
  }

  public function getPostById(int $postId): ?array {
    return $this->postRepository->getPostByID($postId);
  }

  public function getOwnershipById(int $postId): ?int {
    return $this->postRepository->getOwnership($postId);
  }

  private static function databaseExceptionHandler(Throwable $e): array {
    $errors= [
      \Doctrine\DBAL\Exception\ConnectionException::class => 'Connection Error!',
      \Doctrine\DBAL\Exception\UniqueConstraintViolationException::class => 'Slug Already exists. Choose another title.',
      \Doctrine\DBAL\Exception\SyntaxErrorException::class => 'Syntax Error. The developer messed up!'
    ];

    $message = $errors[get_class(object: $e)] ?? 'General Error';

    return array('Status' => 0, 'Message' => $message);
  }

}