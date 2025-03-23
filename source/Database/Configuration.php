<?php

// source\Database\Configuration.php

declare(strict_types=1);

namespace Kouak\BackOfficeApp\Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Configuration
{
    private static $pdo = null;

    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            $projectRoot = dirname(__DIR__, 2);
            $dotenv = Dotenv::createImmutable($projectRoot);
            $dotenv->load();

            $host = $_ENV['DB_HOST'];
            $dbname = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'] ?? '';

            try {
                self::$pdo = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
