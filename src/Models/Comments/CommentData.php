<?php

namespace Lovillela\BlogApp\Models\Comments;


final class CommentData{

  //Campos da tabela user_comment_post
  public readonly int $commentId;
  public readonly ?int $parentId;
  public readonly string $content;
  public readonly string $createdAt;

  //Campo da tabela user 
  public readonly string $userName;

  //Respostas dos posts que serão preenchidas aqui
  public array $replies = [];

  public function __construct(int $commentId, 
                              ?int $parentId,
                              string $content,
                              string $createdAt,
                              string $userName) {
    $this->commentId = $commentId;
    $this->parentId = $parentId;
    $this->content = $content;
    $this->createdAt = $createdAt;
    $this->userName = $userName;
  }
}