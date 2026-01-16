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
                              InputSanitizationService $sanitizationService ,Connection $connection){
    
    $this->postRepository = $postRepository;
    $this->slugService = $slugService;
    $this->sanitizationService = $sanitizationService;
    $this->connection = $connection;
    
    if (isset($user)) {
      $this->user = $user;
      $this->userID = $this->getUserID();
      $this->entityType = 'post';
    }
  }

  public function create(string $title, string $text, int $userID): array{
    
    /**
     * Verificar Usuário
     */
    
    $this->regularUserCheck();

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

  public function getAllPosts(){
    return $this->postRepository->getAllPosts();
  }

  public function getPostBySlug(string $slug){
    return $this->postRepository->getPostBySlug($slug);
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

  private function getUserID(): int{
    global $connection;
    //So the IDE can display all the methods, etc
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $connection;

    $sqlStatment_GetUserID = $connection->prepare($this->selectUserID_Query);
    $sqlStatment_GetUserID->bindValue(1, $this->user);

    return $sqlStatment_GetUserID->executeQuery()->fetchOne();
  }

  private static function regularUserCheck()  {
    if ($_SESSION['role'] != 3) {
      session_destroy();
      header('Location: /');
      exit();
    }
  }

}