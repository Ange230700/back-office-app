<?php

namespace Kouak\BackOfficeApp\Controllers\Login;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\Login\LoginManager;

class LoginController
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var LoginManager
     */
    private $loginManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->loginManager = new LoginManager($pdo);
    }

    /**
     * Authenticate a user using email and password.
     *
     * @param string $email
     * @param string $password
     * @return array|null Returns the user record if authentication succeeds; otherwise null.
     */
    public function authenticate(string $email, string $password): ?array
    {
        try {
            $user = $this->loginManager->getUserByEmail($email);
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                return $user;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
