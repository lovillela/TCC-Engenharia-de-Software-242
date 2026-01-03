<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Repositories\SlugRepository;
use Lovillela\BlogApp\Utils\InputSanitization;

final class SlugService {

  private SlugRepository $slugRepository;

  public function __construct(SlugRepository $slugRepository) {
    $this->slugRepository = $slugRepository;
  }
  
  public function create(string $entity, string $title): string {
    /*
    * Replaces blank spaces with a dash '-'
    * Substitui espaços em branco com traço '-'
    */
    $slugURL = str_replace(' ', '-', strtolower($title));
    $slugURL = InputSanitization::urlInputSanitize($slugURL);

    /*
    * If the slug for the entity already exists
    * Se a slug URL para a entidade já existir
    */
    if ($this->slugRepository->exists($entity, $slugURL)) {
      $slugURL = $slugURL . '-' . bin2hex(random_bytes(5));
    }

    return $slugURL;
  }

  public function getContentId(string $entity, string $slug){
    return $this->slugRepository->findEntityId($entity, $slug);
  }
}
