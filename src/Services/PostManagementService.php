<?php 

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Utils\InputSanitization;
use Throwable;
use Lovillela\BlogApp\Services\SlugService;
class PostManagementService {

  private string $user;
  private int $userID;
  private string $insertPostQuery = 'INSERT INTO `post` (`title`, `content`, `slug`) VALUES (?, ?, ?)';
  private string $selectUserID_Query = 'SELECT `id` FROM `users` WHERE `username` = ?';
  private string $insertPost_Users_Query = 'INSERT INTO `post_users` VALUES (?, ?) ';
  private string $insertID_EntityType_Slug_Query = 'INSERT INTO `slug_map` VALUES (?, ?, ?)';
  private string $selectAllPosts = 'SELECT `title`, `content` FROM `post` LIMIT 50';
  private string $selectPost = 'SELECT `title`, `content` FROM `post` WHERE `id` = ?';
  private $slugService;
  private readonly string $entityType; //Check construct
  public function __construct(?string $user){
    if (isset($user)) {
      $this->user = $user;
      $this->userID = $this->getUserID();
      $this->slugService = new SlugService();
      $this->entityType = 'post';
    }
  }

  public function create(string $title, string $text): array{
    
    $this->regularUserCheck();
    global $connection;
    //So the IDE can display all the methods, etc
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $connection;

    $title = InputSanitization::postTitleSanitize($title);
    $text = InputSanitization::postContentSanitize($text);

    try {
      $connection->beginTransaction();

      $sqlStatment_PostCreation = $connection->prepare($this->insertPostQuery);
      $sqlStatment_PostCreation->bindValue(1, $title);
      $sqlStatment_PostCreation->bindValue(2, $text);
      $slugURL = $this->slugService->create($title,  $this->entityType);
      $sqlStatment_PostCreation->bindValue(3, $slugURL);
      $sqlStatment_PostCreation->executeQuery();

      $postID = $connection->lastInsertId();
      $sqlStatment_PostUsers = $connection->prepare($this->insertPost_Users_Query);
      $sqlStatment_PostUsers->bindValue(1, $this->userID);
      $sqlStatment_PostUsers->bindValue(2, $postID);
      $sqlStatment_PostUsers->executeQuery();

      $sqlStatment_SlugMap = $connection->prepare($this->insertID_EntityType_Slug_Query);
      $sqlStatment_SlugMap->bindValue(1, $postID);
      $sqlStatment_SlugMap->bindValue(2, $this->entityType);
      $sqlStatment_SlugMap->bindValue(3, $slugURL);
      $sqlStatment_SlugMap->executeQuery();
      
      $connection->commit();
      return (array('Status' => 1, 'Message' => 'Post Created Succesfully!'));

    }catch(Throwable $e){
        $connection->rollBack();
        return $this->databaseExceptionHandler($e);
    }
  }

  public function getAllPosts(){
     global $connection;
    //So the IDE can display all the methods, etc
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $connection;

    $posts = $connection->executeQuery($this->selectAllPosts);
    $posts = $posts->fetchAllAssociative();

    return $posts;
  }

  public function getPost(string $slug){
     global $connection;
    //So the IDE can display all the methods, etc
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $connection;

    $slugService = new SlugService();
    $postId = $slugService->getContentId($slug, 'post');
    
    try {
      $getPostContent = $connection->prepare($this->selectPost);
      $getPostContent->bindValue(1, $postId);
      return $getPostContent->executeQuery()->fetchAssociative();
    } catch (Throwable $th) {
      throw $th;
    }
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