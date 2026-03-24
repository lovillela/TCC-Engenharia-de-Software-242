<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$router = new AltoRouter();

$router->setBasePath('/admin/');

$router->map('GET', '', 'AdminController#index', 'admin');
$router->map('POST', '', 'AuthController#login');
$router->map('GET', 'dashboard/', 'AdminController#dashboard', 'adminDashboard');
$router->map('GET', 'logout/', 'AuthController#logout');
$router->map('GET', 'dashboard/create/user/', 'AdminController#userCreatorForm');
$router->map('POST', 'dashboard/create/user/', 'AdminController#createUserAction');
$router->map('GET', 'dashboard/list/posts/', 'AdminController#getAllUsersPosts');
$router->map('POST', 'dashboard/post/delete/[:postId]', 'AdminController#deletePostByAdminAction');
$router->map('GET', 'dashboard/list/users/', 'AdminController#getAllUsers');
$router->map('POST', 'dashboard/user/delete/[:userId]', 'AdminController#deleteUserAction');

return $router;