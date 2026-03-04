<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lovillela\BlogApp\Services\CsrfService;
use Lovillela\BlogApp\Services\SlugService;
use Lovillela\BlogApp\Services\SessionService;
use Lovillela\BlogApp\Services\RedirectService;
use Lovillela\BlogApp\Services\RouteMatchService;
use Lovillela\BlogApp\Services\ViewRenderService;
use Lovillela\BlogApp\Repositories\PostRepository;
use Lovillela\BlogApp\Repositories\SlugRepository;
use Lovillela\BlogApp\Repositories\UserRepository;
use Lovillela\BlogApp\Services\AuthManagerService;
use Lovillela\BlogApp\Services\AuthorizationService;
use Lovillela\BlogApp\Services\PostManagementService;
use Lovillela\BlogApp\Services\UserManagementService;
use Lovillela\BlogApp\Services\InputSanitizationService;
use Lovillela\BlogApp\Services\AuthenticationControlService;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use Monolog\Logger;

/** @var \Doctrine\DBAL\Connection $connection */
$connection = require_once __DIR__ . '/../../src/Services/DatabaseConnectionService.php';

$logger = new Logger('BlogAppLog');
$logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log'));

$renderService = new ViewRenderService();

$redirectService = new RedirectService();

$userRepository = new UserRepository($connection);

$sanitizationService = new InputSanitizationService();

$slugRepository = new SlugRepository($connection);
$slugService = new SlugService($slugRepository, $sanitizationService);

$postRepository = new PostRepository($connection);
$postService = new PostManagementService($postRepository, 
                                            $slugService, 
                                    $sanitizationService, 
                                             $connection,
                                                 $logger);

$userService = new UserManagementService($userRepository,
                                            $postService,
                                    $sanitizationService, 
                                             $connection,
                                                 $logger);

$authenticationService = new AuthenticationControlService($userService);

$authorizationService = new AuthorizationService($postService,
                                                 $userService);

$csrfService = new CsrfService();
$sessionService = new SessionService();
$sessionService->start();

$authManagerService = new AuthManagerService($sessionService, 
                                            $authenticationService, 
                                            $authorizationService,
                                            $csrfService,
                                            $logger);
                                          
$authManagerService->setCsrfToken();                                            
                                          
/**
 * Container de dependências para uso nos controllers
 */
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
  'AuthManagerService' => $authManagerService,
  'RedirectService' => $redirectService,
  'CsrfService' => $csrfService,
  'ViewRenderService' => $renderService,
  LoggerInterface::class => $logger,
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