<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Config\Session\SessionTime;

final class SessionService {

  private static int $idleTimeout;
  private static int $forceTimeout;
  public function __construct() {
    $this::$idleTimeout = SessionTime::IDLE_TIMEOUT->value;
    $this::$forceTimeout = SessionTime::ABSOLUTE_TIMEOUT->value;
  }
}