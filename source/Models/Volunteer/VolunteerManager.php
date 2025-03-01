<?php

namespace Kouak\BackOfficeApp\Models\Volunteer;

use PDO;
use PDOException;

class VolunteerManager
{
    /**
     * @var PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new volunteer.
     *
     * @param string $submittedName
     * @param string $submittedEmail
     * @param string $hashedPassword
     * @param string $submittedRole
     *
     * @return int|null The ID of the newly created volunteer.
     *
     * @throws PDOException
     */
    public function createVolunteer(string $submittedName, string $submittedEmail, string $hashedPassword, string $submittedRole): ?int
    {
        $sql = "INSERT INTO benevoles (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedName, $submittedEmail, $hashedPassword, $submittedRole])) {
            return null;
        }
        return $this->pdo->lastInsertId();
    }

    /**
     * Retrieve full details of volunteers with pagination.
     *
     * @return array [volunteersList, numberOfPages]
     *
     * @throws PDOException
     */
    public function readVolunteersFullDetailsPaginated(): array
    {
        $paginationParams = \Kouak\BackOfficeApp\Utilities\Helpers::getPaginationParams();
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
                LIMIT :limit OFFSET :offset;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $paginationParams['limit'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $paginationParams['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $volunteersList = $stmt->fetchAll();

        $sqlCount = "SELECT COUNT(*) AS total FROM benevoles";
        $stmtCount = $this->pdo->prepare($sqlCount);
        $stmtCount->execute();
        $result = $stmtCount->fetch(PDO::FETCH_ASSOC);
        $total = $result['total'];
        $numberOfPages = ceil($total / $paginationParams['limit']);

        return [$volunteersList, $numberOfPages];
    }

    /**
     * Get the list of volunteers.
     *
     * @return array|null
     *
     * @throws PDOException
     */
    public function readVolunteersList(): ?array
    {
        $sql = "SELECT id, nom FROM benevoles ORDER BY nom";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetchAll();
    }

    /**
     * Get editable fields of a volunteer.
     *
     * @param int $volunteerId
     *
     * @return array
     *
     * @throws PDOException
     */
    public function readEditableFieldsOfVolunteer(int $volunteerId): array
    {
        $sql = "SELECT id, role FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            throw new PDOException("Erreur lors de la récupération du bénévole.");
        }
        return $stmt->fetch();
    }

    /**
     * Update volunteer's role.
     *
     * @param string $submittedRole
     * @param int    $volunteerId
     *
     * @throws PDOException
     */
    public function updateVolunteer(string $submittedRole, int $volunteerId): void
    {
        $sql = "UPDATE benevoles SET role = COALESCE(?, role) WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedRole, $volunteerId])) {
            throw new PDOException("Erreur lors de la mise à jour du rôle.");
        }
    }

    /**
     * Delete a volunteer.
     *
     * @param int $volunteerId
     *
     * @throws PDOException
     */
    public function deleteVolunteer(int $volunteerId): void
    {
        $sql = "DELETE FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            throw new PDOException("Erreur lors de la suppression du bénévole.");
        }
    }
}
