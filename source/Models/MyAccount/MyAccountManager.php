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

    public function getAccount($userId)
    {
        $sql = "SELECT nom, email, mot_de_passe FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$userId])) {
            throw new PDOException("Erreur lors de la récupération des données de l'utilisateur.");
        }
        return $stmt->fetch();
    }

    public function updateAccount($userId, $nom, $email)
    {
        $sql = "UPDATE benevoles SET nom = COALESCE(?, nom), email = COALESCE(?, email) WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$nom, $email, $userId])) {
            throw new PDOException("Erreur lors de la mise à jour des données de l'utilisateur.");
        }
    }

    public function updatePassword($userId, $hashedPassword)
    {
        $sql = "UPDATE benevoles SET mot_de_passe = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$hashedPassword, $userId])) {
            throw new PDOException("Erreur lors de la mise à jour du mot de passe.");
        }
    }
}
