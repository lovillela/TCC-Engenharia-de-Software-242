<?php

namespace Lovillela\BlogApp\Controllers;

use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\RedirectService;

final class PostController {

  private PostManagementService $postService;
  /**maybe some 
   * attributes*/
  private $messages = array();
  private $posts = array();
  private $data = array();

  public function __construct(array $dependencyContainer) {
    $this->postService = $dependencyContainer['PostManagementService'];
  }

  public function index() {
    $this->messages = [
      'title' => 'Post Home',
      'headerText' => 'Post Home',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $this->posts = $this->getAllPosts();

    /**array (size=2) array of arrays
  0 => 
    array (size=2)
      'title' => string 'Blog Post' (length=9)
      'content' => string 'This is test post' (length=17)
  1 => 
    array (size=2)
      'title' => string 'Lorem Ipsum' (length=11)
      'content' => string '
 */

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostHomeView.php');
    $render->render($this->messages, $this->posts);
  }

  public function show($slug) {

    $this->data = $this->getPostBySlug($slug);
    
    $this->messages = [
      'title' => $this->data['title'],
      'headerText' => $this->data['title'],
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostView.php');
    $render->render($this->messages, $this->data);
  }

  public function redirectToTrailingSlash($slug) {
    RedirectService::redirectToTrailingSlash();
  }

  public function addPostAction() {
    $this->regularUserCheck();

    $userID = $_SESSION['userID'];
    $user = $_SESSION['user'];
    $title = $_POST['postTitle'];
    $text = $_POST['blogPost'];

    $post = $this->postService;
    $response = $post->create($title, $text, $userID);

     $this->messages = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => $response['Message'],
      'generalMessage' => '',
    ];

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostFormView.php');
    $render->render($this->messages);
  }

  public function addPostForm() {
    $this->regularUserCheck();

    $this->messages = [
      'title' => 'Post Form',
      'headerText' => 'Post Form',
      'errorMessage' => '',
      'generalMessage' => '',
    ];

    $render = new ViewRenderService(__DIR__ . '/../Views/Frontend/PostFormView.php');
    $render->render($this->messages);
  }

  private function getPostBySlug(string $slug){
    $getPost = $this->postService;
    return $getPost->getPostBySlug($slug);
  }

  private function getAllPosts(){
    $getAllPosts = $this->postService;
    return $getAllPosts->getAllPosts();
  }
  
  private static function regularUserCheck()  {
    if ($_SESSION['role'] != 3) {
      session_destroy();
      header('Location: /');
      exit();
    }
  }
}