<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;

class SlugRepository{

  private Connection $connection;
  private string $checkExistsSlugQuery = 'SELECT 1 FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';
  private string $getEntityIdQuery = 'SELECT `entity_id` FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';
  private string $insertSlugMap = 'INSERT INTO `slug_map` (`entity_id`, `entity_type`, `slug`) VALUES (?, ?, ?)';

  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  public function exists(string $entity, string $slug): bool {

    $existsStmt = $this->connection->prepare($this->checkExistsSlugQuery);
    $existsStmt->bindValue(1, $entity);
    $existsStmt->bindValue(2, $slug);
    
    return (bool) $existsStmt->executeQuery()->fetchOne();
  }

  public function findEntityId(string $entity, string $slug){

    $entityIdSearch = $this->connection->prepare($this->getEntityIdQuery);
    $entityIdSearch->bindValue(1, $entity);
    $entityIdSearch->bindValue(2, $slug);

    $searchResult = $entityIdSearch->executeQuery()->fetchOne();

    return $searchResult ? (int) $searchResult : null;
  }

  public function save(string $entity, string $slug, int $id): int {

    $insertSlugMapStmt = $this->connection->prepare($this->insertSlugMap);
    //id, entity, slug
    $insertSlugMapStmt->bindValue(1, $id);
    $insertSlugMapStmt->bindValue(2, $entity);
    $insertSlugMapStmt->bindValue(3, $slug);

    return $insertSlugMapStmt->executeStatement();
  }
}