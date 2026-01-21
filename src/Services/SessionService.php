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

  /**
   * Previne o acesso de cookies por JS
   * Cookies serão enviados por requisições HTTP
   * Sessões não inicializadas serão rejeitadas
   * Força o uso de cookies para o gerenciamento de sessão
   * Prevents cookie access by JS
   * Cookies will be sent via HTTP requests
   * Unitialized sessions will be rejected
   * Forces cookie use for session management
   */
  private function sessionConfig(){
    ini_set('session.cookie_httponly', 1); 
    ini_set('session.cookie_secure', 1);
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
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