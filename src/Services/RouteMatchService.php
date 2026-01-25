<?php

namespace Lovillela\BlogApp\Services;
use Lovillela\BlogApp\Services\InputSanitizationService;

class RouteMatchService
{
  private $router;
  private $route;
  private $dependencyContainer;
  private InputSanitizationService $inputSanitization;

  public function __construct($router, $route, array $dependencyContainer) {
    $this->router = $router;
    $this->dependencyContainer = $dependencyContainer;
    $this->inputSanitization = $dependencyContainer['InputSanitizationService'];
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

    $controller = new $controllerClass($this->dependencyContainer);

    return \call_user_func_array([$controller, $method], $routeMatch['params']);
  }

private function sanitize($route) {
  return $this->inputSanitization->urlRouteSanitize($route);
}
}  