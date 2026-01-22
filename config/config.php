<?php

return [
  'db' => [
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'name' => $_ENV['DB_NAME'] ?? 'job_dating_youcode',
    'user' => $_ENV['DB_USER'] ?? 'root',
    'pass' => $_ENV['DB_PASS'] ?? '',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
  ]
];
