<?php

use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

/*Creates a Dotenv instance in immutable mode, meaning once the environment 
variables are loaded, they cannot be changed during runtime.*/
$dotEnv= Dotenv::createImmutable(__DIR__ . '/../../config/');
$dotEnv->load();


// Database connection parameters from the .env file
$dataBaseParameters = [
  'dbname' => $_ENV['DB_NAME'],
  'user' => $_ENV['DB_USER'],
  'password' => $_ENV['DB_PASSWORD'],
  'host' => $_ENV['DB_HOST'],
  'port' => $_ENV['DB_PORT'],
  'driver' => $_ENV['DB_DRIVER'],
  'charset' => $_ENV['DB_CHARSET'],
  'collation' => $_ENV['DB_COLLATION'],
];

$connection = DriverManager::getConnection($dataBaseParameters, null);

return $connection;