<?php

namespace Kouak\BackOfficeApp\Controllers\Login;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\Login\LoginManager;

class LoginController
{
    private $pdo;
    private $loginManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->loginManager = new LoginManager($pdo);
    }

    public function getUserByEmail(string $email, string $password): ?array
    {
        try {
            $user = $this->loginManager->readUserByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return null;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
