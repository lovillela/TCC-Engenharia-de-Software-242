<?php
require_once __DIR__ . '/../src/Kernel/kernel.php';

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$routerMain = require_once __DIR__ . '/../config/Routes/main.php';
$routerAdmin = require_once __DIR__ . '/../config/Routes/admin.php';

if (str_starts_with($urlPath, '/admin/')) {
  AdminRouteHandler($urlPath, $dependencyContainer, $routerAdmin) ;
}else{
  RouteHandler($urlPath, $dependencyContainer, $routerMain);
}