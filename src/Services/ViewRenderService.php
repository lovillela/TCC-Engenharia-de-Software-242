<?php

namespace Lovillela\BlogApp\Services;

use Exception;

class ViewRenderService{

  private $viewFile;

  public function __construct(string $requestedView){
    $this->viewFile = $requestedView;
  }

  public function render(array $messages, ?array $data = NULL){

    extract($messages);

    if (isset($data)) {
      extract($data);
    }
    
    if (!(file_exists($this->viewFile))) {
      throw new Exception((string)$e . "\nView file not found", 1);
    }

    $this->addSecurityHeaders();

    return (include $this->viewFile);
  }

  /**
   * Função de segurança para CSP, caso o HtmlPurifier não funcione 
   * CSP security function, just in case HtmlPurifier does not work
   * @return void
   */
  private function addSecurityHeaders(){

    if (headers_sent()) {
      return;
    }

    /**
     * Para forçar o navegador a respeitar o tipo MIME em Content-Type
     * Used to force the browser to respect the MIME type on Content-Type
     */
    header("X-Content-Type-Options: nosniff");
    /**
     * Mesmo que iframe, embed, etc. não sejam permitidos é melhor bloquear a possibilidade de 'clickjacking'
     * Even if iframe, embed, etc are not allowed, it is for the best to block the chance of a clickjacking to happen
     */
    header("X-Frame-Options: SAMEORIGIN");

    /**
     * Política CSP: apenas conteúdos, scripts e estilos do mesmo domínio serão carregados.
     * No caso de object, apenas por garantia mesmo. Será ajustado se necessário.
     * CSP policy: content, scripts and styles from the same doamin will be loaded.
     * In the case of an object, just to be safe. Will be adjuestd if necessary.
     */
    header("Content-Security-Policy: default-src 'self';
                    script-src 'self';
                    object-src 'none';
                    style-src 'self';");

  }
}