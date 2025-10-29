<?php
// filepath: c:\Program Files\Ampps\www\blog-app.com\setup\migrations\migrations-db.php

use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables
$dotEnv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotEnv->load();

// Return just the database parameters
return [
    'dbname' => $_ENV['DB_NAME'],
    'user' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASSWORD'],
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'],
    'driver' => $_ENV['DB_DRIVER'],
    'charset' => $_ENV['DB_CHARSET']
];