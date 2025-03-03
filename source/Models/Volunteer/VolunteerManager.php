<?php

namespace Kouak\BackOfficeApp\Models\Volunteer;

use PDO;
use \Kouak\BackOfficeApp\Utilities\Helpers;

class VolunteerManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createVolunteer(string $submittedName, string $submittedEmail, string $hashedPassword, string $submittedRole): ?int
    {
        $sql = "INSERT INTO Volunteer (username, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedName, $submittedEmail, $hashedPassword, $submittedRole])) {
            return null;
        }
        return $this->pdo->lastInsertId();
    }

    public function readDetailedVolunteersList(): ?array
    {
        $paginationParams = Helpers::getPaginationParams();
        $sql = "SELECT
                    Volunteer.volunteer_id,
                    Volunteer.username,
                    Volunteer.email,
                    Volunteer.role,
                    COALESCE(
                        GROUP_CONCAT(
                            CONCAT(
                                CollectionEvent.collection_place, ' (',
                                DATE_FORMAT(CollectionEvent.collection_date, '%d/%m/%Y'),
                                ')'
                            )
                            SEPARATOR ', '
                        ),
                        'Aucune participation pour le moment'
                    ) AS participations
                FROM Volunteer
                LEFT JOIN Volunteer_Collection ON Volunteer.volunteer_id = Volunteer_Collection.id_volunteer
                LEFT JOIN CollectionEvent ON CollectionEvent.collection_id = Volunteer_Collection.id_collection
                GROUP BY Volunteer.volunteer_id
                ORDER BY Volunteer.username ASC
                LIMIT ? OFFSET ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$paginationParams['limit'], $paginationParams['offset']]);
        return $stmt->fetchAll();
    }

    public function readNumberOfVolunteers(): ?int
    {
        $sql = "SELECT COUNT(*) AS total FROM Volunteer";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function readVolunteersFullDetailsPaginated(): array
    {
        $volunteersList = $this->readDetailedVolunteersList();
        $numberOfPages = ceil($this->readNumberOfVolunteers() / Helpers::getPaginationParams()['limit']);
        return [$volunteersList, $numberOfPages];
    }

    public function readVolunteersList(): ?array
    {
        $sql = "SELECT volunteer_id, username FROM Volunteer ORDER BY username";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetchAll();
    }

    public function readEditableFieldsOfVolunteer(int $volunteerId): ?array
    {
        $sql = "SELECT volunteer_id, role FROM Volunteer WHERE volunteer_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            return null;
        }
        return $stmt->fetch();
    }

    public function updateVolunteer(string $submittedRole, int $volunteerId): ?int
    {
        $sql = "UPDATE Volunteer SET role = COALESCE(?, role) WHERE volunteer_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedRole, $volunteerId])) {
            return null;
        }
        return $stmt->rowCount();
    }

    public function deleteVolunteer(int $volunteerId): ?int
    {
        $sql = "DELETE FROM Volunteer WHERE volunteer_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            return null;
        }
        return $stmt->rowCount();
    }
}
