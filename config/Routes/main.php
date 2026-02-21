<?php
//Routes definition file

require_once __DIR__ . '/../../vendor/autoload.php';

$router = new AltoRouter();

//basepath
$router->setBasePath('');

$router->map(method: 'GET', route: '/', target: 'HomeController#index', name: 'home');
$router->map(method: 'GET', route: '/blog/', target: 'BlogController#index', name: 'blog');
$router->map(method: 'GET', route: '/post/', target: 'PostController#index', name: 'post');
$router->map('GET', '/logout/', 'AuthController#logout');
$router->map('POST', '/login/', 'AuthController#login', 'regularLogin');
$router->map('GET', '/signup/', 'RegularUserController#signUpPage', 'regularSignUpPage');
$router->map('POST', '/signup/', 'RegularUserController#signUpAction', 'regularSignUpAction');
$router->map('GET', '/login/', 'RegularUserController#index', 'regularUserIndex');
$router->map(method: 'GET', route: '/dashboard/post/add/', target: 'PostController#addPostForm', name: 'addPostForm');
$router->map(method: 'GET', route: '/dashboard/post/edit/[:postId]', target: 'PostController#editPostForm', name: 'editPostForm');
$router->map('GET', '/dashboard/', 'RegularUserController#dashboard', 'regularUserDashboard');
$router->map(method: 'POST', route: '/dashboard/post/add/', target: 'PostController#addPostAction', name: 'addPostAction');
$router->map(method: 'POST', route: '/dashboard/post/edit/[:postId]', target: 'PostController#editPostAction', name: 'updatePostAction');
//Individual Posts
$router->map(method: 'GET', route: '/post/[:slug]/', target: 'PostController#show', name: 'showPost');
$router->map(method: 'GET', route: '/post/[:slug]', target: 'PostController#redirectToTrailingSlash', name: 'showPostRedirect');

return $router;