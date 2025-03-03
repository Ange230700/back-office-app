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
        $sql = "INSERT INTO benevoles (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
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
                    benevoles.id,
                    benevoles.nom,
                    benevoles.email,
                    benevoles.role,
                    COALESCE(
                        GROUP_CONCAT(
                            CONCAT(
                                collectes.lieu, ' (',
                                DATE_FORMAT(collectes.date_collecte, '%d/%m/%Y'),
                                ')'
                            )
                            SEPARATOR ', '
                        ),
                        'Aucune participation pour le moment'
                    ) AS participations
                FROM benevoles
                LEFT JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
                LEFT JOIN collectes ON collectes.id = benevoles_collectes.id_collecte
                GROUP BY benevoles.id
                ORDER BY benevoles.nom ASC
                LIMIT ? OFFSET ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$paginationParams['limit'], $paginationParams['offset']]);
        return $stmt->fetchAll();
    }

    public function readNumberOfVolunteers(): ?int
    {
        $sql = "SELECT COUNT(*) AS total FROM benevoles";
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
        $sql = "SELECT id, nom FROM benevoles ORDER BY nom";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetchAll();
    }

    public function readEditableFieldsOfVolunteer(int $volunteerId): ?array
    {
        $sql = "SELECT id, role FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            return null;
        }
        return $stmt->fetch();
    }

    public function updateVolunteer(string $submittedRole, int $volunteerId): ?int
    {
        $sql = "UPDATE benevoles SET role = COALESCE(?, role) WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedRole, $volunteerId])) {
            return null;
        }
        return $stmt->rowCount();
    }

    public function deleteVolunteer(int $volunteerId): ?int
    {
        $sql = "DELETE FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            return null;
        }
        return $stmt->rowCount();
    }
}
