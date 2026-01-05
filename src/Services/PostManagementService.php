<?php 

namespace Lovillela\BlogApp\Services;

use Doctrine\DBAL\Connection;
use Lovillela\BlogApp\Repositories\PostRepository;
use Lovillela\BlogApp\Utils\InputSanitization;
use Throwable;
use Lovillela\BlogApp\Services\SlugService;

class PostManagementService {

  private PostRepository $postRepository;
  private SlugService $slugService;
  private Connection $connection;
  private const ENTITY = 'post';

  public function __construct(PostRepository $postRepository, SlugService $slugService, Connection $connection){
    
    $this->postRepository = $postRepository;
    $this->slugService = $slugService;
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

    $title = InputSanitization::postTitleSanitize($title);
    $text = InputSanitization::postContentSanitize($text);

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