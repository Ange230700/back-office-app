<?php

namespace Kouak\BackOfficeApp\Models\Login;

use PDO;

class LoginManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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
