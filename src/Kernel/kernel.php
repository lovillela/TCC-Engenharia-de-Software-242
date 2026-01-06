<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lovillela\BlogApp\Repositories\PostRepository;
use Lovillela\BlogApp\Repositories\SlugRepository;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\RouteMatchService;
use Lovillela\BlogApp\Services\SlugService;

/*
Prevents JavaScript from accessing the session cookie
Cookie is only sent via HTTP(S) requests, not accessible via document.cookie
*/
ini_set('session.cookie_httponly', 1); 
ini_set('session.cookie_secure', 1);      // HTTPS only (for production)

/**PHP will reject uninitialized session IDs
Forces session ID regeneration for unknown IDs */
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict'); //CSRF Attack Prevention

ini_set('session.gc_maxlifetime', 1440);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


/** @var \Doctrine\DBAL\Connection $connection */
$connection = require_once __DIR__ . '/../../src/Services/DatabaseConnectionService.php';

$slugRepository = new SlugRepository($connection);
$slugService = new SlugService($slugRepository);

$postRepository = new PostRepository($connection);
$postService = new PostManagementService($postRepository, $slugService, $connection);

$dependencyContainer = [
  'Connection' => $connection,
  'SlugService' => $slugService,
  'SlugRepository' => $slugRepository,
  'PostManagementService' => $postService,
  'PostRepository' => $postRepository,
];

$routerMain = require_once __DIR__ . '/../../config/Routes/main.php';
$routerAdmin = require_once __DIR__ . '/../../config/Routes/admin.php';

function RouteHandler($path){
  global $routerMain;

  $routerService = new RouteMatchService($routerMain, $path);
  $routerService->routeMatch();
}

function AdminRouteHandler($path){
  global $routerAdmin;

  $routerService = new RouteMatchService($routerAdmin, $path);
  $routerService->routeMatch();
}