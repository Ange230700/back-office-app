<?php

declare(strict_types=1);

namespace Kouak\BackOfficeApp\Database;

class Configuration
{
    /**
     * @var \PDO|null
     */
    private static $pdo = null;

    /**
     * Returns a singleton PDO instance.
     *
     * @return \PDO
     */
    public static function getPdo(): \PDO
    {
        if (self::$pdo === null) {
            $host = '127.0.0.1';
            $dbname = 'gestion_collectes';
            $username = 'root';
            $password = '';

            try {
                self::$pdo = new \PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                    $username,
                    $password,
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (\PDOException $e) {
                die("Erreur de connexion : " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
