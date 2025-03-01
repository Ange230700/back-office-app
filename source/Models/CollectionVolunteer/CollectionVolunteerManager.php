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

    /**
     * Get the list of collection IDs the volunteer attended.
     *
     * @param int $volunteerId
     * @return array
     *
     * @throws PDOException
     */
    public function readCollectionsListVolunteerAttended($volunteerId): ?array
    {
        $sql = "SELECT id_collecte FROM benevoles_collectes WHERE id_benevole = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            return null;
        }
        $results = $stmt->fetchAll();
        return array_column($results, 'id_collecte');
    }

    /**
     * Create volunteer participations.
     *
     * @param array $submittedParticipations
     * @param int   $volunteerId
     *
     * @throws PDOException
     */
    public function createVolunteerParticipation($submittedParticipations, $volunteerId)
    {
        if (!empty($submittedParticipations)) {
            $sql = "INSERT INTO benevoles_collectes (id_benevole, id_collecte) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            foreach ($submittedParticipations as $collectionId) {
                if (!$stmt->execute([$volunteerId, $collectionId])) {
                    return null;
                }
            }
            return $stmt->rowCount();
        }
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

    public function deleteCollectionsVolunteerAttended($volunteerId): ?int
    {
        $sql = "DELETE FROM benevoles_collectes WHERE id_benevole = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
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

    public function updateCollectionsVolunteerAttended($volunteerId, $collectionsAttended)
    {
        $this->deleteCollectionsVolunteerAttended($volunteerId);
        if (!empty($collectionsAttended)) {
            $this->createVolunteerParticipation($collectionsAttended, $volunteerId);
        }
    }
}
