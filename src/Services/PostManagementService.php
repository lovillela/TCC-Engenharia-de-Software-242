<?php 

namespace Lovillela\BlogApp\Services;

use Doctrine\DBAL\Connection;
use Exception;
use Lovillela\BlogApp\Repositories\PostRepository;
use Throwable;
use Lovillela\BlogApp\Services\SlugService;
use Lovillela\BlogApp\Services\InputSanitizationService;
use Psr\Log\LoggerInterface;

class PostManagementService {

  private PostRepository $postRepository;
  private SlugService $slugService;
  private InputSanitizationService $sanitizationService;
  private Connection $connection;
  private LoggerInterface $logger;
  private const ENTITY = 'post';
  private const BATCH_SIZE = 1000;

  public function __construct(PostRepository $postRepository, 
                              SlugService $slugService, 
                              InputSanitizationService $sanitizationService, 
                              Connection $connection,
                              LoggerInterface $logger){
    
    $this->postRepository = $postRepository;
    $this->slugService = $slugService;
    $this->sanitizationService = $sanitizationService;
    $this->connection = $connection;
    $this->logger = $logger;
  }

  public function create(string $title, string $text, int $userID): array{
    
    $title = $this->sanitizationService->postTitleSanitize($title);
    $text = $this->sanitizationService->postContentSanitize($text);

    try {
      $this->connection->beginTransaction();

      $slugURL = $this->slugService->create($this::ENTITY, $title);

      $postID = $this->postRepository->save($title, $text, 
                              $slugURL, $userID);
      
      $this->slugService->save($this::ENTITY, $slugURL, $postID);

      $this->connection->commit();

      return ['status' => true, 'message' => 'Post criado com sucesso!', 'title' => $title,'text' => $text];
      

    }catch(Throwable $th){
      $this->connection->rollBack();
      $this->logger->error('Erro ao criar post!', []);
      return ['status' => false, 'message' => $th->getMessage()];
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

      return ['status' => true, 'message' => 'Post deletado com sucesso!'];
      
    } catch (Throwable $th) {
      $this->connection->rollBack();
      $this->logger->error('Erro ao deletar post!', ['id' => $postId]);
      return ['status' => false, 'message' => $th->getMessage()];
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
      
      return ['status' => true, 
              'message' => 'Post atualizado com sucesso!',
              'title' => $title,
              'text' => $text,
              'slug' => $slug,
              'postId' => $postId];
      
    } catch (Throwable $th) {
        $this->connection->rollBack();
        $this->logger->warning('Erro ao atualizar post!', ['id' => $postId]);
        return ['status' => false, 'message' => $th->getMessage()];
    }
  }

  public function deleteAllUserPostsByUserId(int $userId) {
    /**
     * Nota: beginTransaction, commit e rollback estão comentados
     * para evitar problemas com a orquestração no user service
     */
    try {
      //$this->connection->beginTransaction();

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

      //$this->connection->commit();
    } catch (Throwable $th) {
        //$this->connection->rollBack();
        $this->logger->warning('Erro ao deletar posts do usuário!', ['userId' => $userId, 'exception' => $th->getMessage()]);
        throw new Exception('Erro ao deletar posts do usuário!', 0 , $th);
    }
  }

  public function deletePostByAdmin(int $postId) {
    try {
      $this->connection->beginTransaction();
      $this->postRepository->deletePostCategoriesInRange([$postId]);
      $this->postRepository->deletePostTagsInRange([$postId]);
      $this->postRepository->deletePostCommentsInRange([$postId]);
      $this->postRepository->deletePostReactionsInRange([$postId]);
      $this->postRepository->deleteAllUsersFromPost($postId);
      $this->postRepository->delete($postId);
      $this->slugService->deleteInRange([$postId], $this::ENTITY);
      $this->connection->commit();
    } catch (Throwable $th) {
        $this->connection->rollBack();
        $this->logger->error('Erro ao deletar post pelo admin!', ['id' => $postId, 'exception' => $th->getMessage()]);
        return ['status' => false, 'message' => $th->getMessage()];
    }
  }

  public function getAllPosts(): ?array{

    try {
      return $this->postRepository->getAllPosts();
    } catch (Throwable $th) {
      $this->logger->warning('Erro ao ler todos os posts!', []);
      return [];
    }
  }

  public function getPostBySlug(string $slug): ?array {

    try {
      $post = $this->postRepository->getPostBySlug($slug);

      return $post ? $this->sanitizationService->displayPostSanitize($post) : null;

    } catch (Throwable $th) {
      $this->logger->warning('Erro ao ler post por slug!', ['slug' => $slug]);
      return null;
    }    
  }

  public function getPostById(int $postId): ?array {
    try {
      $post = $this->postRepository->getPostByID($postId);

      return $post ? $this->sanitizationService->displayPostSanitize($post) : null;

    } catch (Throwable $th) {
      $this->logger->warning('Erro ao ler o post por id!', ['postId' => $postId]);
      return null;
    }

  }

  public function getAllPostsIdsAndTitlesByUserId(int $userId): ?array{

    try {
      $userPostsData = [];
      $userPostsIdsAndTitles = $this->postRepository->getAllPostsIdsAndTitlesByUserId($userId);

      if (!isset($userPostsIdsAndTitles)) {
        return null;
      }

      foreach ($userPostsIdsAndTitles as $userPost) {
        $userPost = $this->sanitizationService->displayPostSanitize($userPost);
        array_push($userPostsData, ['id' => $userPost['id'], 'title' => $userPost['title']]);
      }

      return isset($userPostsData) ? $userPostsData : null;
    } catch (Throwable $th) {
      $this->logger->notice('Erro ao ler posts do usuário', ['userId' => $userId]);
      return null;
    }
    
  }

  public function getAllPostsIdsAndTitlesForAdmin() {
    try {
      $allUsersPostsIdsAndTitles= $this->postRepository->getAllPostsIdsAndTitlesForAdmin();
      $allUsersPostsData = [];

      if (!isset($allUsersPostsIdsAndTitles)) {
        return null;
      }

      foreach ($allUsersPostsIdsAndTitles as $userPost) {
        $userPost = $this->sanitizationService->displayPostSanitize($userPost);
        array_push($allUsersPostsData, ['id' => $userPost['id'] ,'title' => $userPost['title']]);
      }

      return isset($allUsersPostsData) ? $allUsersPostsData : null;
    } catch (Throwable $th) {
      $this->logger->error('Erro ao ler id e títulos de posts de usuários');
      return null;
    }
  }

  public function getOwnershipById(int $postId): ?int {
    try {
      return $this->postRepository->getOwnership($postId);
    } catch (Throwable $th) {
      $this->logger->warning('Erro ao ler autoria do post!', ['postId' => $postId]);
      return null;
    }
  }
  
}