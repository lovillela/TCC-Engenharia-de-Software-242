<?php

namespace Lovillela\BlogApp\Config\Session;

/**
 * Tempo (em segundos) para o funcionamento da sessão.
 * Time (in seconds) for session timeout.
 */
enum SessionTime: int {
    case IDLE_TIMEOUT = 1800;
    case ABSOLUTE_TIMEOUT = 14400;
}