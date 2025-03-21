<?php
// source\Database\migration.php

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Get database connection settings from .env
$dbHost = $_ENV['DB_HOST'];
$dbUser = $_ENV['DB_USER'];
$dbPassword = $_ENV['DB_PASS'];
$dbName = $_ENV['DB_NAME'];

// Path to your SQL schema file
$schemaFile = __DIR__ . '/collections_management.sql';
if (!file_exists($schemaFile)) {
    die("Schema file not found: $schemaFile\n");
}
$sql = file_get_contents($schemaFile);

try {
    // Connect without specifying a database (needed to drop/create the database)
    $pdo = new PDO("mysql:host=$dbHost;charset=utf8mb4", $dbUser, $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Drop the existing database (if any), then create and use the new one
    $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");
    echo "Dropped database $dbName\n";
    $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "Created database $dbName\n";
    $pdo->exec("USE `$dbName`");

    // Execute the SQL schema (which may contain multiple statements)
    $pdo->exec($sql);
    echo "Database schema imported from $schemaFile\n";
} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Migration completed successfully.\n";
