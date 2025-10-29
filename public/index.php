<?php
require_once __DIR__ . '/../src/Kernel/kernel.php';

$urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($urlPath, '/admin/')) {
  AdminRouteHandler($urlPath);
}else{
  RouteHandler($urlPath);
}