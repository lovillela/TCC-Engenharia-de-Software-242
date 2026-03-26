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
   * Função recursiva para montar o html dos comentários
   */
  public function renderComments(array $renderPartialViewData, int $depth = 0) : string {
    
    $comments = $renderPartialViewData['comments'];
    $renderedComments = '';

    foreach ($comments as $comment) {
      /*Margem dos comentários baseada na profundidade (respostas)*/
      $margin = $depth > 0 ? 'ms-2 border-start ps-3' : '';

      /**
       * Estrutura base do comentário
       */

      $renderedComments .= '<div class="mb-3 ' . $margin . '">' .
                            '<div class="d-flex justify-content-between align-items-center">' .
                            '<strong>' . $comment->userName  . '</strong>' .
                            '<small class="text-muted">' . date('d/m/Y H:i', strtotime($comment->createdAt)) . '</small>' . 
                            '</div>' . 
                            ' <p class="mb-1 text-break">' . $comment->content . '</p>';
      /**
       * Fim - Estrutura base do comentário
       */

      /**Ações (deletar, responder) */

      $renderedComments .= '<div class="d-flex gap-3 mt-1">';

      /**
       * Se logado, mostra o botão de resposta
       */
      if($renderPartialViewData['isLoggedIn']){
        $renderedComments .= '<button class="btn btn-link btn-sm p-0 text-decoration-none' . 
                              'data-bs-toggle="collapse" data-bs-target="#reply-' . $comment->commentId . '">' . 
                              $renderPartialViewData['replyButtonText']  . '</button>';
      }

      if ($renderPartialViewData['isAdminOrModerator']) {
        $renderedComments .= '<form action="/post/comment/delete/' . $comment->commentId . '" method="POST"' .
                             'class="d-inline" onsubmit="return confirm(\'Deletar este comentário e suas respostas?\')">' .
                             '<input type="hidden" name="csrfToken" value="' . $renderPartialViewData['csrfToken'] . '">' .
                             '<button type="submit" class="btn btn-link btn-sm p-0 text-danger text-decoration-none">Deletar</button>' .
                             '</form>';
      }

      $renderedComments .= '</div>'; //Fechando o bloco de ações
      /**
       * Fim Ações (deletar, responder) 
       */

      /**
       * Recursão para preencher os filhos, usando as respostas
       */

      if(!empty($comment->replies)){
        $renderPartialViewData['comments'] = $comment->replies; //os comentários são atualizados com as respostas
        $renderedComments .= '<div class="mt-2">' . 
                             $this->renderComments($renderPartialViewData, $depth + 1) .
                             '</div>';
      }

      //Fechamento da div base
      $renderedComments .= '</div>';
    }

    return $renderedComments;
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
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; connect-src 'self';");

  }
}