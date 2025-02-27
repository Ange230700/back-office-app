<?php

namespace Kouak\BackOfficeApp\Models\MyAccount;

use PDO;
use PDOException;

class MyAccountManager
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * Constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve the account details for a given user.
     *
     * @param int $userId
     * @return array
     * @throws PDOException
     */
    public function getAccount($userId)
    {
        $sql = "SELECT nom, email, mot_de_passe FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$userId])) {
            throw new PDOException("Erreur lors de la récupération des données de l'utilisateur.");
        }
        return $stmt->fetch();
    }

    /**
     * Update the account's name and email.
     *
     * @param int    $userId
     * @param string $nom
     * @param string $email
     *
     * @return void
     * @throws PDOException
     */
    public function updateAccount($userId, $nom, $email)
    {
        $sql = "UPDATE benevoles SET nom = COALESCE(?, nom), email = COALESCE(?, email) WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$nom, $email, $userId])) {
            throw new PDOException("Erreur lors de la mise à jour des données de l'utilisateur.");
        }
    }

    /**
     * Update the account's password.
     *
     * @param int    $userId
     * @param string $hashedPassword
     *
     * @return void
     * @throws PDOException
     */
    public function updatePassword($userId, $hashedPassword)
    {
        $sql = "UPDATE benevoles SET mot_de_passe = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$hashedPassword, $userId])) {
            throw new PDOException("Erreur lors de la mise à jour du mot de passe.");
        }
    }
}
