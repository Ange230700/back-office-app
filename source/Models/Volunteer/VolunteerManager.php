<?php

namespace Kouak\BackOfficeApp\Models\Volunteer;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Utilities\Helpers;

class VolunteerManager
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

    /* ------------------- CREATE ------------------- */

    /**
     * Create a new volunteer.
     *
     * @param string $submittedName
     * @param string $submittedEmail
     * @param string $hashedPassword
     * @param string $submittedRole
     *
     * @return int The ID of the newly created volunteer.
     *
     * @throws PDOException
     */
    public function createVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole)
    {
        $sql = "INSERT INTO benevoles(nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedName, $submittedEmail, $hashedPassword, $submittedRole])) {
            throw new PDOException("Erreur lors de l'insertion du bénévole.");
        }
        return $this->pdo->lastInsertId();
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
                    throw new PDOException("Erreur lors de l'insertion des participations.");
                }
            }
        }
    }

    /* ------------------- READ ------------------- */

    /**
     * Retrieve full details of volunteers with pagination.
     *
     * @return array [volunteersList, numberOfPages]
     * @throws PDOException
     */
    public function readVolunteersFullDetailsPaginated()
    {
        $paginationParams = Helpers::getPaginationParams();
        $sql = "SELECT
                    benevoles.id,
                    benevoles.nom,
                    benevoles.email,
                    benevoles.role,
                    COALESCE(
                        GROUP_CONCAT(CONCAT(collectes.lieu, ' (', collectes.date_collecte, ')') SEPARATOR ', '),
                        'Aucune participation pour le moment'
                    ) AS participations
                FROM benevoles
                LEFT JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
                LEFT JOIN collectes ON collectes.id = benevoles_collectes.id_collecte
                GROUP BY benevoles.id
                ORDER BY benevoles.nom ASC
                LIMIT :limit OFFSET :offset";
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
     * @return array
     *
     * @throws PDOException
     */
    public function readVolunteersList()
    {
        $sql = "SELECT id, nom FROM benevoles ORDER BY nom";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            throw new PDOException("Erreur lors de la récupération des bénévoles.");
        }
        return $stmt->fetchAll();
    }

    /**
     * Get the list of collection IDs the volunteer attended.
     *
     * @param int $volunteerId
     *
     * @return array
     */
    public function readCollectionsListVolunteerAttended($volunteerId)
    {
        $sql = "SELECT id_collecte FROM benevoles_collectes WHERE id_benevole = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$volunteerId]);
        $results = $stmt->fetchAll();
        // Return only the collection IDs using array_column
        return array_column($results, 'id_collecte');
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
    public function readEditableFieldsOfVolunteer($volunteerId)
    {
        $sql = "SELECT id, role FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            throw new PDOException("Erreur lors de la récupération du bénévole.");
        }
        return $stmt->fetch();
    }

    /* ------------------- UPDATE ------------------- */

    /**
     * Update volunteer's role.
     *
     * @param string $submittedRole
     * @param int    $volunteerId
     *
     * @throws PDOException
     */
    public function updateVolunteer($submittedRole, $volunteerId)
    {
        $sql = "UPDATE benevoles SET role = COALESCE(?, role) WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedRole, $volunteerId])) {
            throw new PDOException("Erreur lors de la mise à jour du rôle.");
        }
    }

    /**
     * Update volunteer participations.
     *
     * @param int   $volunteerId
     * @param array $submittedParticipations
     *
     * @throws PDOException
     */
    public function updateVolunteerParticipations($volunteerId, $submittedParticipations)
    {
        try {
            // Delete existing participations.
            $sqlDelete = "DELETE FROM benevoles_collectes WHERE id_benevole = ?";
            $stmtDelete = $this->pdo->prepare($sqlDelete);
            if (!$stmtDelete->execute([$volunteerId])) {
                throw new PDOException("Erreur lors de la suppression des participations.");
            }
            // Insert new participations if any.
            if (!empty($submittedParticipations)) {
                $sqlInsert = "INSERT INTO benevoles_collectes (id_benevole, id_collecte) VALUES (?, ?)";
                $stmtInsert = $this->pdo->prepare($sqlInsert);
                foreach ($submittedParticipations as $collectionId) {
                    if (!$stmtInsert->execute([$volunteerId, $collectionId])) {
                        throw new PDOException("Erreur lors de l'assignation des collectes.");
                    }
                }
            }
        } catch (PDOException $e) {
            throw new PDOException("Erreur de base de données : " . $e->getMessage());
        }
    }

    /* ------------------- DELETE ------------------- */

    /**
     * Delete a volunteer.
     *
     * @param int $volunteerId
     *
     * @throws PDOException
     */
    public function deleteVolunteer($volunteerId)
    {
        $sql = "DELETE FROM benevoles WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$volunteerId])) {
            throw new PDOException("Erreur lors de la suppression du bénévole.");
        }
    }
}
