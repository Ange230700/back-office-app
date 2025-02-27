<?php

namespace Kouak\BackOfficeApp\Controllers\MyAccount;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\MyAccount\MyAccountManager;

class MyAccountController
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var MyAccountManager
     */
    private $manager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->manager = new MyAccountManager($pdo);
    }

    /**
     * Retrieve account details.
     *
     * @param int $userId
     * @return array
     */
    public function getAccount($userId)
    {
        return $this->manager->getAccount($userId);
    }

    /**
     * Update account information, including password if provided.
     *
     * @param int    $userId
     * @param string $nom
     * @param string $email
     * @param string $currentPassword  (plain text; if empty, password update is skipped)
     * @param string $newPassword      (plain text)
     * @param string $confirmPassword  (plain text)
     *
     * @return string|null  Returns an error message if any, or null on success.
     */
    public function updateAccount($userId, $nom, $email, $currentPassword, $newPassword, $confirmPassword)
    {
        // Retrieve current account information (including stored hash)
        $account = $this->manager->getAccount($userId);
        if (!$account) {
            return "Utilisateur introuvable.";
        }

        // If passwords are provided, validate them.
        if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
            if (!password_verify($currentPassword, $account['mot_de_passe'])) {
                return "Le mot de passe actuel est incorrect.";
            }
            if ($newPassword !== $confirmPassword) {
                return "Le nouveau mot de passe et la confirmation ne correspondent pas.";
            }
            // Hash new password and update.
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->manager->updatePassword($userId, $hashedPassword);
        }

        // Update name and email
        $this->manager->updateAccount($userId, $nom, $email);
        return null;
    }
}
