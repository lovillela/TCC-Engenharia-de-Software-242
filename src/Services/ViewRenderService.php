<?php

namespace Lovillela\BlogApp\Services;

use Exception;
use Lovillela\BlogApp\Models\Views\ViewData;
use Lovillela\BlogApp\Config\Views\ViewPath;

final class ViewRenderService{

  public function __construct(){
  }

  public function render(ViewData $viewData){

    if (!(file_exists($viewData->viewFilePath))) {
      //throw new Exception((string)$e . "\nView file not found", 1);
      exit();
    }
    
    /**
     * Inicia a bufferização
     * Initiates buffering
     */
    ob_start();

    if (!empty($viewData->bodyData)) {
      $localbodyData = $viewData->bodyData;
      extract($localbodyData);
    }

    include $viewData->viewFilePath;

    $headTitle = $viewData->headTitle;
    $baseView = ViewPath::BASE_VIEW->getPath();

    $this->addSecurityHeaders();

    /**
     * Termina a bufferização e limpeza
     * Finishes buffering and cleanup
     */
    $renderedContent = ob_get_clean();

    return (include $baseView);
  }

  /**
   * Função de segurança para CSP, caso o HtmlPurifier não funcione 
   * CSP security function, just in case HtmlPurifier does not work
   * @return void
   */
  private function addSecurityHeaders(){

    /**
     * Para evitar o erro 'headers already sent'
     * To prevent the error 'headers already sent'
     */
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
     * Nota: quebras de linha não são permitidas.
     * CSP policy: content, scripts and styles from the same doamin will be loaded.
     * In the case of an object, just to be safe. Will be adjuestd if necessary.
     * Note: line breaks are not allowed.
     */
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js; object-src 'none'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.min.css https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css;");

  }
}