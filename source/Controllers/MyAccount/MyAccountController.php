<?php

namespace Kouak\BackOfficeApp\Controllers\MyAccount;

use PDO;
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
        $error = null;
        $account = $this->manager->getAccount($userId);
        if (!$account) {
            $error = "Utilisateur introuvable.";
        } else {
            if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                if (!password_verify($currentPassword, $account['mot_de_passe'])) {
                    $error = "Le mot de passe actuel est incorrect.";
                } elseif ($newPassword !== $confirmPassword) {
                    $error = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $this->manager->updatePassword($userId, $hashedPassword);
                }
            }
            if (!$error) {
                $this->manager->updateAccount($userId, $nom, $email);
            }
        }
        return $error;
    }
}
