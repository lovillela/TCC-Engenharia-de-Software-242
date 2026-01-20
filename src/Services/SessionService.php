<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Config\Session\SessionTime;

final class SessionService {

  private static int $idleTimeout;
  private static int $forceTimeout;
  public function __construct() {
    $this::$idleTimeout = SessionTime::IDLE_TIMEOUT->value;
    $this::$forceTimeout = SessionTime::ABSOLUTE_TIMEOUT->value;
    $this->sessionConfig();
  }

  private function sessionConfig(){
    ini_set('session.cookie_httponly', 1); 
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Strict'); 
  }

  public function start() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  public function regenerate() {
    session_regenerate_id(true);
  }
}