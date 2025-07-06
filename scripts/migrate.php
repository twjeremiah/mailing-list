<?php

use Doctrine\DBAL\DriverManager;

require __DIR__ . '/../vendor/autoload.php';

$connectionParameters = [
    'driver'   => 'pdo_mysql',
    'host'     => 'mysql',
    'dbname'   => 'mailing-list',
    'user'     => 'test',
    'password' => 'test',
];

$connection = DriverManager::getConnection($connectionParameters);

$sql = "CREATE TABLE IF NOT EXISTS contact (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email_address VARCHAR(254) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);";

try {
    $connection->executeStatement($sql);
    echo "Migration ran successfully. 'contact' table created";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage();
}