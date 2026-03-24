<?php

namespace Lovillela\BlogApp\Repositories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;
use Throwable;
use Exception;

class SlugRepository{

  private Connection $connection;
  private LoggerInterface $logger;
  private string $checkExistsSlugQuery = 'SELECT 1 FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';
  private string $getEntityIdQuery = 'SELECT `entity_id` FROM `slug_map` WHERE (`entity_type` = ? AND `slug` = ?)';
  private string $insertSlugMap = 'INSERT INTO `slug_map` (`entity_id`, `entity_type`, `slug`) VALUES (?, ?, ?)';
  private string $deleteFromSlugMapInRange = 'DELETE FROM `slug_map` WHERE `entity_id` IN (?) AND `entity_type` = (?)';

  public function __construct(Connection $connection, LoggerInterface $logger) {
    $this->connection = $connection;
    $this->logger = $logger;
  }

  public function exists(string $entity, string $slug): bool {

    try {
      $existsStmt = $this->connection->prepare($this->checkExistsSlugQuery);
      $existsStmt->bindValue(1, $entity);
      $existsStmt->bindValue(2, $slug);
      
      return (bool) $existsStmt->executeQuery()->fetchOne();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao verificar existência do slug!', 
                                ['entity' => $entity, 'slug' => $slug, 'exception' => $th]);
          throw new Exception('Erro ao verificar existência do slug!');     
    }  
  }

  public function findEntityId(string $entity, string $slug){

    try {
      $entityIdSearch = $this->connection->prepare($this->getEntityIdQuery);
      $entityIdSearch->bindValue(1, $entity);
      $entityIdSearch->bindValue(2, $slug);

      $searchResult = $entityIdSearch->executeQuery()->fetchOne();

      return $searchResult ? (int) $searchResult : null;
    } catch (Throwable $th) {
        $this->logger->error('Erro ao verificar o id da entidade', 
                                ['entity' => $entity, 'slug' => $slug, 'exception' => $th]);
          throw new Exception('Erro ao verificar o id da entidade');     
    }

  }

  public function save(string $entity, string $slug, int $id) {

    try {
      $insertSlugMapStmt = $this->connection->prepare($this->insertSlugMap);
      //id, entity, slug
      $insertSlugMapStmt->bindValue(1, $id);
      $insertSlugMapStmt->bindValue(2, $entity);
      $insertSlugMapStmt->bindValue(3, $slug);

      $insertSlugMapStmt->executeStatement();
    } catch (Throwable $th) {
        $this->logger->error('Erro ao salvar o slug!', 
                                ['id' => $id, 'entity' => $entity, 'slug' => $slug, 'exception' => $th]);
          throw new Exception('Erro ao salvar o slug!');     
    }
  }

  public function deleteSlugsInRange(array $entityIds, string $entity): bool {

    try {
      return $this->connection->executeStatement($this->deleteFromSlugMapInRange,
                                          [$entityIds, $entity],
                                          [ArrayParameterType::INTEGER,
                                                  ParameterType::STRING]);
    } catch (Throwable $th) {
        $this->logger->error('Erro ao deletar slugs!', 
                                ['entity' => $entity, 'entityIds' => $entityIds,'exception' => $th]);
          throw new Exception('Erro ao deletar slugs!');     
    }

  }
}