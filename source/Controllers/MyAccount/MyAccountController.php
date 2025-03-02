<?php

namespace Kouak\BackOfficeApp\Controllers\MyAccount;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\MyAccount\MyAccountManager;

class MyAccountController
{
    private $pdo;

    private $manager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->manager = new MyAccountManager($pdo);
    }

    public function getAccount($userId)
    {
        return $this->manager->getAccount($userId);
    }

    public function updateAccount($userId, $nom, $email, $currentPassword, $newPassword, $confirmPassword)
    {
        $account = $this->manager->getAccount($userId);
        if (!$account) {
            return "Utilisateur introuvable.";
        }

        if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
            if (!password_verify($currentPassword, $account['mot_de_passe'])) {
                return "Le mot de passe actuel est incorrect.";
            }
            if ($newPassword !== $confirmPassword) {
                return "Le nouveau mot de passe et la confirmation ne correspondent pas.";
            }
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->manager->updatePassword($userId, $hashedPassword);
        }

        $this->manager->updateAccount($userId, $nom, $email);
        return null;
    }
}
