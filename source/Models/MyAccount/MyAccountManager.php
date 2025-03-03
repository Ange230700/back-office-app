<?php

namespace Kouak\BackOfficeApp\Models\MyAccount;

use PDO;
use PDOException;

class MyAccountManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function readAccount($userId): ?array
    {
        $sql = "SELECT nom, email, mot_de_passe FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$userId])) {
            return null;
        }
        return $stmt->fetch();
    }

    public function updateAccount($userId, $nom, $email): ?int
    {
        $sql = "UPDATE benevoles SET nom = COALESCE(?, nom), email = COALESCE(?, email) WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$nom, $email, $userId])) {
            return null;
        }
        return $stmt->rowCount();
    }

    public function updatePassword($userId, $hashedPassword): ?int
    {
        $sql = "UPDATE benevoles SET mot_de_passe = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$hashedPassword, $userId])) {
            return null;
        }
        return $stmt->rowCount();
    }
}
