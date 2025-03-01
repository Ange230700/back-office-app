<?php

namespace Kouak\BackOfficeApp\Models\CollectionVolunteer;

use PDO;

class CollectionVolunteerManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function readVolunteersListWhoAttendedCollection($collectionId): ?array
    {
        $sql = "SELECT id_benevole FROM benevoles_collectes WHERE id_collecte = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function assignVolunteersToCollection($collectionId, $volunteersAssigned): ?int
    {
        $sql = "INSERT INTO benevoles_collectes (id_collecte, id_benevole) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($volunteersAssigned as $volunteerId) {
            if (!$stmt->execute([$collectionId, $volunteerId])) {
                return null;
            }
        }
        return $stmt->rowCount();
    }

    public function deleteVolunteersFromCollection($collectionId): ?int
    {
        $sql = "DELETE FROM benevoles_collectes WHERE id_collecte = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->rowCount();
    }

    public function updateVolunteersParticipation($collectionId, $volunteersAssigned)
    {
        $this->deleteVolunteersFromCollection($collectionId);
        if (!empty($volunteersAssigned)) {
            $this->assignVolunteersToCollection($collectionId, $volunteersAssigned);
        }
    }
}
