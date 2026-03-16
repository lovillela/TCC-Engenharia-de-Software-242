<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Repositories\SlugRepository;
use Lovillela\BlogApp\Services\InputSanitizationService;

final class SlugService {

  private SlugRepository $slugRepository;
  private InputSanitizationService $sanitizationService;

  public function __construct(SlugRepository $slugRepository, InputSanitizationService $sanitizationService) {
    $this->slugRepository = $slugRepository;
    $this->sanitizationService = $sanitizationService;
  }
  
  public function create(string $entity, string $title): string {
    /*
    * Replaces blank spaces with a dash '-'
    * Substitui espaços em branco com traço '-'
    */
    $slugURL = str_replace(' ', '-', strtolower($title));
    $slugURL = $this->sanitizationService->slugSanitize($slugURL);

    /*
    * If the slug for the entity already exists
    * Se a slug URL para a entidade já existir
    */
    if ($this->slugRepository->exists($entity, $slugURL)) {
      $slugURL = $slugURL . '-' . bin2hex(random_bytes(5));
    }

    return $slugURL;
  }

  public function update(string $entity, string $title) : string {

    $slugURL = str_replace(' ', '-', strtolower($title));
    $slugURL = $this->sanitizationService->slugSanitize($slugURL);

    if ($this->slugRepository->exists($entity, $slugURL)) {
      $slugURL = $slugURL . '-' . bin2hex(random_bytes(5));
    }

    return $slugURL;
  }

  public function deleteInRange(array $entityIds, string $entity) {
    $this->slugRepository->deleteSlugsInRange($entityIds, $entity);
  }

  public function findContentId(string $entity, string $slug): int|null{
    return $this->slugRepository->findEntityId($entity, $slug);
  }

  public function save(string $entity, string $slug, int $id) {
    $this->slugRepository->save($entity, $slug, $id);
  }
}
