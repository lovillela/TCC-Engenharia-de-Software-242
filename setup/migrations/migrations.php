<?php
// filepath: c:\Program Files\Ampps\www\blog-app.com\setup\migrations\migrations.php

return [
  'table_storage' => [
    'table_name' => 'migrations',
    'version_column_name' => 'version',
    'version_column_length' => 256,
    'executed_at_column_name' => 'executed_at',
    'execution_time_column_name' => 'execution_time',
  ],
  'migrations_paths' => [
    'Doctrine\Migrations' => __DIR__,
  ],
  'all_or_nothing' => true,
  'check_database_platform' => true,
];