<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Utils\InputSanitization;

class RouteMatchService
{
    private $router;
    private $route;
    public function __construct($router, $route) {
      $this->router = $router;
      $this->route = $this->sanitize($route);
    }
    public function routeMatch() {

      $routeMatch = $this->router->match($this->route);

        if (!$routeMatch) {
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            echo 'Page not found';
            return false;
        } 
            // Parse controller#method format
        list($controller, $method) = explode(separator: '#', string: $routeMatch['target']);

        $controllerClass = "Lovillela\\BlogApp\\Controllers\\{$controller}";

        if (!(class_exists($controllerClass) && method_exists($controllerClass, $method))) {
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            echo 'Controller or method not found';
            return false;
        }

        if ($controller === 'AuthController' && str_contains($this->route,  'login')) {
          if (str_contains($this->route,  'admin')) {
            $controller = new $controllerClass(1);
            //new conditionals will be necessary for other logins
          }elseif(str_contains($this->route,  '')){
            $controller = new $controllerClass(3);
          }
        }else {
          $controller = new $controllerClass();
        }

      return call_user_func_array([$controller, $method], $routeMatch['params']);
    }

  private function sanitize($route) {
    return InputSanitization::urlRouteSanitize($route);
  }
}  