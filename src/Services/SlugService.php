<?php

namespace Lovillela\BlogApp\Services;

use Lovillela\BlogApp\Utils\InputSanitization;

final class SlugService {

  private string $slugURL;
  private string $entityType;
  private string $checkDuplicatedSlugSQL_Query = 'SELECT 1 FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';
  private string $getEntityId = 'SELECT `entity_id` FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';
  private array $entityTypes = ['post', 'category', 'tag'];
  
  public function create(string $title, string $entityType): string {
    //replaces blank spaces with a dash '-'
    $this->entityType =  $entityType;
    $this->slugURL = str_replace(' ', '-', strtolower($title));
    $this->slugURL = InputSanitization::urlInputSanitize($this->slugURL);

    //if the slug for the entity already exists
    if ($this->checkDuplicate()) {
      $this->slugURL = $this->slugURL . '-' . bin2hex(random_bytes(5));
    }

    return $this->slugURL;
  }

  private function checkDuplicate() {
    global $connection;
    //So the IDE can display all the methods, etc
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $connection;

    $duplicateCheck = $connection->prepare($this->checkDuplicatedSlugSQL_Query);
    $duplicateCheck->bindValue(1, $this->entityType);
    $duplicateCheck->bindValue(2, $this->slugURL);
    
    return $duplicateCheck->executeQuery()->fetchOne();
  }

  public function getContentId(string $slug, string $entity){
    global $connection;
    //So the IDE can display all the methods, etc
    /** @var \Doctrine\DBAL\Connection $connection */
    $connection = $connection;

    //if (!(array_search($entity, $this->entityTypes))) {
      //return false;
    //}

    try {
      $entityIdSearch = $connection->prepare($this->getEntityId);
      $entityIdSearch->bindValue(1, $entity);
      $entityIdSearch->bindValue(2, $slug);
      return $entityIdSearch->executeQuery()->fetchOne();
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
