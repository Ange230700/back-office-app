<?php

// source\Models\MyAccount\MyAccountManager.php

namespace Kouak\BackOfficeApp\Models\MyAccount;

use PDO;

class MyAccountManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function readAccount($userId): ?array
    {
        $sql = "SELECT username, email, password FROM Volunteer WHERE volunteer_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$userId])) {
            return null;
        }
        return $stmt->fetch();
    }

    public function updateAccount($userId, $username, $email): ?int
    {
        $sql = "UPDATE Volunteer SET username = COALESCE(?, username), email = COALESCE(?, email) WHERE volunteer_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$username, $email, $userId])) {
            return null;
        }
        return $stmt->rowCount();
    }

    public function updatePassword($userId, $hashedPassword): ?int
    {
        $sql = "UPDATE Volunteer SET password = ? WHERE volunteer_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$hashedPassword, $userId])) {
            return null;
        }
        return $stmt->rowCount();
    }
}
