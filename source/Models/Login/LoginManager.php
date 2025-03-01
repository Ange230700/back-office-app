<?php

namespace Kouak\BackOfficeApp\Models\Login;

use PDO;
use PDOException;

class LoginManager
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve a user record by email.
     *
     * @param string $email
     * @return array|null Returns the user record as an associative array or null if not found.
     * @throws PDOException
     */
    public function getUserByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM benevoles WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([$email])) {
            $user = $stmt->fetch();
            return $user ? $user : null;
        }
        return null;
    }
}
