<?php

// source\Controllers\MyAccount\MyAccountController.php

namespace Kouak\BackOfficeApp\Controllers\MyAccount;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\MyAccount\MyAccountManager;
use Kouak\BackOfficeApp\Utilities\Session;

class MyAccountController
{
    private $pdo;
    private $myAccountManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->myAccountManager = new MyAccountManager($pdo);
    }

    public function getAccount($userId): ?array
    {
        try {
            return $this->myAccountManager->readAccount($userId);
        } catch (PDOException $e) {
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }

    public function editAccount($userId, $username, $email, $currentPassword, $newPassword, $confirmPassword): ?string
    {
        try {
            $error = null;
            $account = $this->myAccountManager->readAccount($userId);
            if (!$account) {
                $error = "Utilisateur introuvable.";
            } else {
                if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                    if (!password_verify($currentPassword, $account['password'])) {
                        $error = "Le mot de passe actuel est incorrect.";
                    } elseif ($newPassword !== $confirmPassword) {
                        $error = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
                    } else {
                        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $this->myAccountManager->updatePassword($userId, $hashedPassword);
                    }
                }
                if (!$error) {
                    $this->myAccountManager->updateAccount($userId, $username, $email);
                }
            }

            if (!$error) {
                Session::setSession("flash_success", "Votre compte a été mis à jour avec succès.");
            } else {
                Session::setSession("flash_error", "Une erreur est survenue lors de la mise à jour de votre compte.");
            }

            return $error;
        } catch (PDOException $e) {
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }
}
