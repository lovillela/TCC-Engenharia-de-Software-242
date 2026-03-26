<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lovillela\BlogApp\Repositories\CommentRepository;
use Lovillela\BlogApp\Services\CommentService;
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
use Monolog\Handler\BufferHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;

/** @var \Doctrine\DBAL\Connection $connection */
$connection = require_once __DIR__ . '/../../src/Services/DatabaseConnectionService.php';

$securityLogger = new Logger('Security');
$securityLogger->pushProcessor(new IntrospectionProcessor());
$securityLogger->pushHandler(new BufferHandler(new StreamHandler(__DIR__ . '/../../logs/security.log')));
$appLogger = new Logger('App');
$appLogger->pushProcessor(new IntrospectionProcessor());
$appLogger->pushHandler(new BufferHandler(new StreamHandler(__DIR__ . '/../../logs/app.log')));

$infraLogger = new Logger('Infrastructure');
$infraLogger->pushProcessor(new IntrospectionProcessor());
$infraLogger->pushHandler(new BufferHandler(new StreamHandler(__DIR__ . '/../../logs/infrastructure.log')));

$renderService = new ViewRenderService();

$redirectService = new RedirectService();

$userRepository = new UserRepository($connection, $infraLogger);

$commentRepository = new CommentRepository($connection, $infraLogger);

$commentService = new CommentService($commentRepository, $appLogger);

$sanitizationService = new InputSanitizationService();

$slugRepository = new SlugRepository($connection, $infraLogger);
$slugService = new SlugService($slugRepository, $sanitizationService);

$postRepository = new PostRepository($connection, $infraLogger);
$postService = new PostManagementService($postRepository, 
                                            $slugService, 
                                    $sanitizationService, 
                                             $connection,
                                                 $appLogger);

$userService = new UserManagementService($userRepository,
                                            $postService,
                                    $sanitizationService, 
                                             $connection,
                                                 $appLogger);

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
                                            $securityLogger);
                                          
$authManagerService->setCsrfToken();                                            
                                          
/**
 * Container de dependências para uso nos controllers
 */
$dependencyContainer = [
  'PostManagementService' => $postService,
  'UserService' => $userService,
  'AuthManagerService' => $authManagerService,
  'RedirectService' => $redirectService,
  'ViewRenderService' => $renderService,
  'InputSanitizationService' => $sanitizationService,
  'CommentService' => $commentService,
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