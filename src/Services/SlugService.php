<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Repositories\SlugRepository;
use Lovillela\BlogApp\Utils\InputSanitization;

final class SlugService {

  private SlugRepository $slugRepository;
  private string $slugURL;
  private string $entity;

  public function __construct(SlugRepository $slugRepository, string $entity) {
    $this->slugRepository = $slugRepository;
    $this->entity = $entity;
  }
  
  public function create(string $title, string $entity): string {
    /*
    * Replaces blank spaces with a dash '-'
    * Substitui espaços em branco com traço '-'
    */
    $this->entity = $entity;
    $this->slugURL = str_replace(' ', '-', strtolower($title));
    $this->slugURL = InputSanitization::urlInputSanitize($this->slugURL);

    /*
    * If the slug for the entity already exists
    * Se a slug URL para a entidade já existir
    */
    if ($this->slugRepository->exists()) {
      $this->slugURL = $this->slugURL . '-' . bin2hex(random_bytes(5));
    }

    return $this->slugURL;
  }

  public function getContentId(string $slug, string $entity){
    return $this->slugRepository->findEntityId($slug, $entity);
  }
}
