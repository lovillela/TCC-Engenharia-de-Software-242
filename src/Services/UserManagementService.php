<?php

namespace Lovillela\BlogApp\Services;

use Doctrine\DBAL\Connection;
use Lovillela\BlogApp\Config\Permissions\UserPermissions;
use Lovillela\BlogApp\Repositories\UserRepository;
use Lovillela\BlogApp\Utils\PasswordHash;
use Lovillela\BlogApp\Services\InputSanitizationService;
use Psr\Log\LoggerInterface;
use Throwable;

class UserManagementService{

  private UserRepository $userRepository;
  private PostManagementService $postService;
  private InputSanitizationService $sanitizationService;
  private Connection $connection;
  private LoggerInterface $logger;

  public function __construct(UserRepository $userRepository,
                              PostManagementService $postService, 
                              InputSanitizationService $sanitizationService,
                              Connection $connection,
                              LoggerInterface $logger){
    $this->userRepository = $userRepository;
    $this->postService = $postService;
    $this->sanitizationService = $sanitizationService;
    $this->connection = $connection;
    $this->logger = $logger;
  }
  
  public function create(string $username, string $password, string $email, int $role /**Account role to be created*/) {

    $username = $this->sanitizationService->usernameSanitize($username);

    try {

      if($this->userRepository->exists($username)){
        return ['status' => false, 'message' => 'Usuário já cadastrado!'];
      }

      if($this->userRepository->emailExists($email)){
        return ['status' => false, 'message' => 'Email já cadastrado!'];
      }

      $password = PasswordHash::hashPassword($password);
      $this->connection->beginTransaction();

      $this->userRepository->create($username, $password, $email, $role);

      $this->connection->commit();

      return ['status' => true, 'message' => 'Usuário criado com sucesso'];
      
    } catch (Throwable $th) {
      $this->connection->rollBack();
      return ['status' => false, 'message' => 'Erro ao criar usuário'];
    }
    
  }

  public function delete(int $userId) {

    $userId = $this->sanitizationService->idSanitize($userId);
    
    if (!$this->userRepository->existsById($userId)) {
      return ['status' => false, 'message' => 'Usuário não encontrado!'];
    }
  
    try {
      $this->connection->beginTransaction();
      $this->userRepository->deleteUserReactions($userId);
      $this->userRepository->deleteUserComments($userId);
      $this->postService->deleteAllUserPostsByUserId($userId);
      $this->userRepository->delete($userId);
      $this->connection->commit();
      return ['status' => true, 'message' => 'Usuário deletado com sucesso!'];
    } catch (Throwable $th) {
      $this->connection->rollBack();
      $this->logger->error('Erro ao deletar usuário', ['userId' => $userId ,'exception' => $th]);
      return ['status' => false, 'message' => 'Error ao deletar usuário'];
    }
    
  }

  public function findByEmail(string $email): ?array {
    return $this->userRepository->findByEmail($email);
  }

  public function getUserPermissionsById(int $userId) {
    return $this->userRepository->getUserPermissionsById($userId);
  }

  public function getAllUsers() : ?array {
    try {
      return $this->userRepository->getAllUsers();
    } catch (Throwable $th) {
      $this->logger->error('Erro ao carregar lista de usuários', [$th->getMessage()]);
      return null;
    }
  }
  
}