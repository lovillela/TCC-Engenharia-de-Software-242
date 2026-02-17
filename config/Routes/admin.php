<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$router = new AltoRouter(basePath: '/admin/');

$router->map('GET', '', 'AdminController#index', 'admin');
$router->map('POST', 'login/', 'AuthController#login', 'adminLogin');
$router->map('GET', 'dashboard/', 'AdminController#dashboard', 'adminDashboard');
$router->map('GET', 'logout/', 'AuthController#logout');
$router->map('GET', 'create/user/', 'AdminController#userCreatorForm');
$router->map('POST', 'create/user/', 'AdminController#createUserAction');

return $router;