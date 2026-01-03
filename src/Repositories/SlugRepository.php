<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;

class SlugRepository{

  private Connection $connection;
  private string $entity;
  private string $slugURL;
  private string $checkExistsSlugQuery = 'SELECT 1 FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';
  private string $getEntityIdQuery = 'SELECT `entity_id` FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';

  public function __construct(Connection $connection, string $entity) {
    $this->connection = $connection;
    $this->entity = $entity;
  }

  public function exists(): bool {
    $existsStmt = $this->connection->prepare($this->checkExistsSlugQuery);
    $existsStmt->bindValue(1, $this->entity);
    $existsStmt->bindValue(2, $this->slugURL);
    
    return (bool) $existsStmt->executeQuery()->fetchOne();
  }

  public function findEntityId(string $slug, string $entity){
    $entityIdSearch = $this->connection->prepare($this->getEntityIdQuery);
    $entityIdSearch->bindValue(1, $entity);
    $entityIdSearch->bindValue(2, $slug);

    $searchResult = $entityIdSearch->executeQuery()->fetchOne();

    return $searchResult ? (int) $searchResult : null;
    }
}