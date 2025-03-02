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

    public function authenticate(string $email, string $password): ?array
    {
        try {
            $user = $this->loginManager->getUserByEmail($email);
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                return $user;
            }
            return null;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
