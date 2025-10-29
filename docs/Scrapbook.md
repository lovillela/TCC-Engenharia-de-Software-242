# Blog CMS Scrapbook

Welcome to the my Scrapbook.

This document details how this system works under the hood.

## Table of Contents

- [Architecture Overview](#architecture-overview)
- [Request Flow](#request-flow)
- [Authentication System](#authentication-system)
- [Routing Rules](#routing-rules)
- [Database Conventions](#database-conventions)
- [Security Guidelines](#security-guidelines)
- [Controller Patterns](#controller-patterns)
- [Service Organization](#service-organization)
- [View Rendering](#view-rendering)
- [Development Conventions](#development-conventions)

## Architecture Overview

### Core Principles

- Single entry point for ALL requests
- "MVC" pattern with Service layer
- Route-based permission detection
- Dependency injection where possible

### Why These Decisions Were Made

I sail to where I shall.

------------------------------------------------------------

# How Scraps Work

  This system contains certain backend and server-side functions (Scraps, in this book).

  While this system kinda follows the MVC pattern, important functions such as `RenderView`, `routeMatch`, etc. are **services**.

## Request Flow

The user request flow happens in a simple and direct way.

Apache (`.htaccess`) will redirect **ALL** incoming requests to the `public/index.php` file.

With this centralized entry point, the `kernel.php` will be loaded.

The `kernel.php` will:

- Start the session
- Load composer's autoload
- Establish the database connection
- Load the route definitions for the Front-end and Admins (`config/Routes`)
- Provides two functions RouteHandler() functions for Front-end and Admins

The previously mentioned `public/index.php` will call both functions to match the appropriate routes.

## Scrap - Routing Service

Underneath, all routes are matched using the **centralized** `routeMatchService.php`

Obviously, if neither the route nor the Controller and/or the method does not exist, the system will throw a `404 Not Found`.

If the Controller and the function exists, its related class will be instantiated and its function called.

**xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx**

**Note: Depending on the route, the class may be instantiated using special parameters**

**xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx**

## Scrap - Authentication System

The authentication process begins with its related routing.

```php
  if (str_contains((string)$this->route,  'admin') && str_contains((string)$this->route,  'login')) {
          $controller = new $controllerClass(1);
          //new conditionals will be necessary for other logins
        }else{
          $controller = new $controllerClass();
        }

return call_user_func_array([$controller, $method], $routeMatch['params']);
```

If the route contains 'login' and the appropriate path (i.e.: admin, user, etc.) it will call the AuthController's login() function.

Based on the route, the permission will be provided to the constructor.

**Note: the controller is NOT responsible for handling the authentication logic itself**

**This is handled by the service**

Then the username and password are sent to the `AuthenticationControlService.php`.

If the credentials + permission matches, the user is authenticated (yay!).

### Permission Levels

- 1: Admin users  
- 2: Moderators (future)
- 3: Regular users

### Scrap - Permission Detection

For example, the createUser() function in the UserManagementService() class is used by both admins and public users, the function bellow verifies if the current user is an admin, if the requested account is either Admin or Moderator.

```php
private static function adminPrivilegeCheck(){
    if($_SESSION['role'] != 1){
      session_destroy();
      header('Location: /');
      exit();
    }
  }
```
I call it SYEff (Simple, Yet Effective).

This function may become a service of its own in the future. Most likely will.

### User Creation

Same service, different contexts
$userMgnt = new UserManagementService();

Admin creates any role
```php
$response = $userMgnt->createUser($username, $password, $email, $adminRole);
```

Public creates role 3 only
```php
$response = $userMgnt->createUser($username, $password, $email, 3);
```

## Scrap - Rendering System

Unlike those fancy frameworks that have their own rendering engines, the creatively named ViewRenderService is SimHub (Simple and Humble).

```php
public function render(array $messages){

    extract($messages);
    
    if (!(file_exists($this->viewFile))) {
      throw new Exception((string)$e . "\nView file not found", 1);
    }

    return (include $this->viewFile);
  }
```

It simply includes the view file with the array $messages to dinamically fill the page.
