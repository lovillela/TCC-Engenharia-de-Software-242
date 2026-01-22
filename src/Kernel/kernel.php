<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lovillela\BlogApp\Repositories\PostRepository;
use Lovillela\BlogApp\Repositories\SlugRepository;
use Lovillela\BlogApp\Repositories\UserRepository;
use Lovillela\BlogApp\Services\AuthenticationControlService;
use Lovillela\BlogApp\Services\AuthorizationService;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\RouteMatchService;
use Lovillela\BlogApp\Services\SlugService;
use Lovillela\BlogApp\Services\InputSanitizationService;
use Lovillela\BlogApp\Services\SessionService;
use Lovillela\BlogApp\Services\UserManagementService;

/** @var \Doctrine\DBAL\Connection $connection */
$connection = require_once __DIR__ . '/../../src/Services/DatabaseConnectionService.php';

$sessionService = new SessionService();
$sessionService->start();

$userRepository = new UserRepository($connection);

$authenticationService = new AuthenticationControlService($userRepository);

$sanitizationService = new InputSanitizationService();

$slugRepository = new SlugRepository($connection);
$slugService = new SlugService($slugRepository, $sanitizationService);

$postRepository = new PostRepository($connection);
$postService = new PostManagementService($postRepository, $slugService, 
                                          $sanitizationService, $connection);

$authorizationService = new AuthorizationService($postRepository);

$userService = new UserManagementService($userRepository, $authenticationService,
                                        $postService,  $connection);

$dependencyContainer = [
  'Connection' => $connection,
  'SlugService' => $slugService,
  'SlugRepository' => $slugRepository,
  'PostManagementService' => $postService,
  'PostRepository' => $postRepository,
  'InputSanitizationService' => $sanitizationService,
  'SessionService' => $sessionService,
  'AuthenticationService' => $authenticationService,
  'UserRepository' => $userRepository,
  'UserService' => $userService,
];

$routerMain = require_once __DIR__ . '/../../config/Routes/main.php';
$routerAdmin = require_once __DIR__ . '/../../config/Routes/admin.php';

function RouteHandler($path, $dependencyContainer){
  global $routerMain;

  $routerService = new RouteMatchService($routerMain, $path, $dependencyContainer);
  $routerService->routeMatch();
}

function AdminRouteHandler($path, $dependencyContainer){
  global $routerAdmin;

  $routerService = new RouteMatchService($routerAdmin, $path, $dependencyContainer);
  $routerService->routeMatch();
}