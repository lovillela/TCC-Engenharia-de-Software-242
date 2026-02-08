<?php

namespace Lovillela\BlogApp\Services;

final class CsrfService {

  public function __construct(){
  }

  public function generate(): string {
    return bin2hex(random_bytes(64));
  }

  public function validate(?string $csrfTokenForm, ?string $csfrTokenSession): bool {

    return (!empty($csfrTokenSession) && !empty($csrfTokenForm))? 
      hash_equals($csrfTokenForm, $csfrTokenSession) : false;

  }
}
