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

    public function createVolunteerParticipation($submittedParticipations, $volunteerId): void
    {
        if (!empty($submittedParticipations)) {
            $sql = "INSERT INTO Volunteer_Collection (id_volunteer, id_collection) VALUES (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            foreach ($submittedParticipations as $collectionId) {
                if (!$stmt->execute([$volunteerId, $collectionId])) {
                    return;
                }
            }
        }
    }

    public function createVolunteerCollectionAssignment($collectionId, $volunteersAssigned): ?int
    {
        $sql = "INSERT INTO Volunteer_Collection (id_collection, id_volunteer) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        foreach ($volunteersAssigned as $volunteerId) {
            if (!$stmt->execute([$collectionId, $volunteerId])) {
                return null;
            }
        }
        return $stmt->rowCount();
    }

    public function readVolunteersListWhoAttendedCollection($collectionId): ?array
    {
        $sql = "SELECT id_volunteer FROM Volunteer_Collection WHERE id_collection = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function readCollectionsListVolunteerAttended($volunteerId): ?array
    {
        $sql = "SELECT id_collection FROM Volunteer_Collection WHERE id_volunteer = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            return null;
        }
        $results = $stmt->fetchAll();
        return array_column($results, 'id_collection');
    }

    public function updateVolunteersInCollection($collectionId, $volunteersAssigned): void
    {
        $this->deleteVolunteersFromCollection($collectionId);
        if (!empty($volunteersAssigned)) {
            $this->createVolunteerCollectionAssignment($collectionId, $volunteersAssigned);
        }
    }

    public function updateCollectionsVolunteerAttended($volunteerId, $collectionsAttended): void
    {
        $this->deleteCollectionsVolunteerAttended($volunteerId);
        if (!empty($collectionsAttended)) {
            $this->createVolunteerParticipation($collectionsAttended, $volunteerId);
        }
    }

    public function deleteVolunteersFromCollection($collectionId): ?int
    {
        $sql = "DELETE FROM Volunteer_Collection WHERE id_collection = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->rowCount();
    }

    public function deleteCollectionsVolunteerAttended($volunteerId): ?int
    {
        $sql = "DELETE FROM Volunteer_Collection WHERE id_volunteer = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            return null;
        }
        return $stmt->rowCount();
    }
}
