<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;

class SlugRepository{

  private Connection $connection;
  private string $checkExistsSlugQuery = 'SELECT 1 FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';
  private string $getEntityIdQuery = 'SELECT `entity_id` FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function exists(string $entity, string $slugURL): bool {
    $existsStmt = $this->connection->prepare($this->checkExistsSlugQuery);
    $existsStmt->bindValue(1, $entity);
    $existsStmt->bindValue(2, $slugURL);
    
    return (bool) $existsStmt->executeQuery()->fetchOne();
  }

  public function findEntityId(string $entity, string $slug){
    $entityIdSearch = $this->connection->prepare($this->getEntityIdQuery);
    $entityIdSearch->bindValue(1, $entity);
    $entityIdSearch->bindValue(2, $slug);

    $searchResult = $entityIdSearch->executeQuery()->fetchOne();

    return $searchResult ? (int) $searchResult : null;
    }
}