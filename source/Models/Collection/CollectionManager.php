<?php

// source\Models\Collection\CollectionManager.php

namespace Kouak\BackOfficeApp\Models\Collection;

use PDO;
use PDOException;

class CollectionManager
{
    private $pdo;

    const ERROR_MSG = "Erreur lors de l'exécution de la requête SQL.";

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new collection and assign volunteers and waste details.
     *
     * @param string $submittedDate
     * @param string $submittedPlace
     * @param array  $volunteersAssigned
     * @param array  $wasteTypesSubmitted
     * @param array  $quantitiesSubmitted
     *
     * @return int The ID of the newly created collection.
     *
     * @throws PDOException on failure.
     */

    public function createCollectionWithDetails($submittedDate, $submittedPlace, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted)
    {
        // Begin transaction for atomicity
        $this->pdo->beginTransaction();
        try {
            // Insert new collection
            $sqlInsertCollection = "INSERT INTO CollectionEvent (date_collecte, lieu) VALUES (?, ?)";
            $stmtCollection = $this->pdo->prepare($sqlInsertCollection);
            if (!$stmtCollection->execute([$submittedDate, $submittedPlace])) {
                throw new PDOException("Erreur lors de l'insertion de la collecte.");
            }
            $collectionId = $this->pdo->lastInsertId();

            // Assign volunteers to the collection
            $sqlAssignVolunteer = "INSERT INTO Volunteer_Collection (id_collecte, id_benevole) VALUES (?, ?)";
            $stmtVolunteer = $this->pdo->prepare($sqlAssignVolunteer);
            foreach ($volunteersAssigned as $volunteerId) {
                if (!$stmtVolunteer->execute([$collectionId, $volunteerId])) {
                    throw new PDOException("Erreur lors de l'assignation des bénévoles.");
                }
            }

            // Insert collected wastes details if provided
            if (!empty($wasteTypesSubmitted) && !empty($quantitiesSubmitted)) {
                $sqlInsertWaste = "INSERT INTO Collected_waste (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)";
                $stmtWaste = $this->pdo->prepare($sqlInsertWaste);
                for ($i = 0; $i < count($wasteTypesSubmitted); $i++) {
                    if (!empty($wasteTypesSubmitted[$i]) && is_numeric($quantitiesSubmitted[$i]) && !$stmtWaste->execute([$collectionId, $wasteTypesSubmitted[$i], $quantitiesSubmitted[$i]])) {
                        throw new PDOException("Erreur lors de l'insertion des déchets collectés.");
                    }
                }
            }

            $this->pdo->commit();
            return $collectionId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function readCollectedWastesTotalQuantity()
    {
        $sql = "SELECT COALESCE(ROUND(SUM(COALESCE(Collected_waste.quantite_kg,0)),1), 0) AS quantite_total_des_dechets_collectes
                FROM CollectionEvent
                LEFT JOIN Collected_waste ON CollectionEvent.id = Collected_waste.id_collecte";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            throw new PDOException(self::ERROR_MSG);
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['quantite_total_des_dechets_collectes'];
    }

    public function readMostRecentCollection()
    {
        $sql = "SELECT lieu, date_collecte
                FROM CollectionEvent
                WHERE date_collecte <= CURDATE()
                ORDER BY date_collecte DESC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            throw new PDOException(self::ERROR_MSG);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readNextCollection()
    {
        $sql = "SELECT lieu, date_collecte
                FROM CollectionEvent
                WHERE date_collecte > CURDATE()
                ORDER BY date_collecte ASC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            throw new PDOException(self::ERROR_MSG);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readCollection($collectionId)
    {
        $sql = "SELECT id, date_collecte, lieu FROM CollectionEvent WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            throw new PDOException("Erreur lors de la récupération de la collecte.");
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readVolunteersListWhoAttendedCollection($collectionId)
    {
        $sql = "SELECT id_benevole FROM Volunteer_Collection WHERE id_collecte = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            throw new PDOException("Erreur lors de la récupération des bénévoles.");
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function readPlacesList()
    {
        $sqlQueryToSelectPlacesList = "SELECT DISTINCT lieu FROM CollectionEvent ORDER BY lieu";
        $statementToGetPlacesList = $this->pdo->prepare($sqlQueryToSelectPlacesList);
        if (!$statementToGetPlacesList->execute()) {
            throw new PDOException("Erreur lors de la récupération des lieux.");
        }
        return $statementToGetPlacesList->fetchAll(PDO::FETCH_COLUMN);
    }

    public function readCollectionsList()
    {
        $sqlQueryToSelectCollectionsList = "SELECT id, CONCAT(DATE_FORMAT(date_collecte, '%d/%m/%Y'), ' - ', lieu) AS collection_label FROM CollectionEvent ORDER BY date_collecte";
        $statementToGetCollectionsList = $this->pdo->prepare($sqlQueryToSelectCollectionsList);
        if (!$statementToGetCollectionsList->execute()) {
            throw new PDOException("Erreur lors de la récupération des CollectionEvent.");
        }
        return $statementToGetCollectionsList->fetchAll();
    }

    public function readCollectionsListPaginated(array $paginationParams)
    {
        $limit = $paginationParams['limit'];
        $offset = $paginationParams['offset'];
        $sql = "SELECT
                    Volunteer_Collection.id_collecte AS id,
                    CollectionEvent.date_collecte,
                    CollectionEvent.lieu,
                    GROUP_CONCAT(DISTINCT benevoles.nom ORDER BY benevoles.nom SEPARATOR ', ') AS benevoles,
                    GROUP_CONCAT(DISTINCT CONCAT(COALESCE(Collected_waste.type_dechet, 'type  (s) non défini(s)'), ' (', ROUND(COALESCE(Collected_waste.quantite_kg, 0), 1), 'kg)')
                    ORDER BY Collected_waste.type_dechet SEPARATOR ', ') AS wasteDetails
                FROM benevoles
                INNER JOIN Volunteer_Collection ON benevoles.id = Volunteer_Collection.id_benevole
                INNER JOIN CollectionEvent ON CollectionEvent.id = Volunteer_Collection.id_collecte
                LEFT JOIN Collected_waste ON CollectionEvent.id = Collected_waste.id_collecte
                GROUP BY Volunteer_Collection.id_collecte
                ORDER BY CollectionEvent.date_collecte DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            throw new PDOException("Erreur lors de la récupération des CollectionEvent.");
        }
        $collectionsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count query
        $sqlCount = "SELECT COUNT(DISTINCT Volunteer_Collection.id_collecte) AS total
                     FROM benevoles
                     INNER JOIN Volunteer_Collection ON benevoles.id = Volunteer_Collection.id_benevole
                     INNER JOIN CollectionEvent ON CollectionEvent.id = Volunteer_Collection.id_collecte";
        $stmtCount = $this->pdo->prepare($sqlCount);
        if (!$stmtCount->execute()) {
            throw new PDOException("Erreur lors de la récupération du nombre de CollectionEvent.");
        }
        $result = $stmtCount->fetch(PDO::FETCH_ASSOC);
        $total = $result['total'];
        $numberOfPages = ceil($total / $limit);

        return [$collectionsList, $numberOfPages];
    }

    public function updateCollection($submittedDate, $submittedPlace, $collectionId)
    {
        $sql = "UPDATE CollectionEvent SET date_collecte = COALESCE(?, date_collecte), lieu = COALESCE(?, lieu) WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedDate, $submittedPlace, $collectionId])) {
            throw new PDOException("Erreur lors de la mise à jour de la collecte.");
        }
    }

    public function updateVolunteersParticipation($collectionId, $volunteersAssigned)
    {
        // Delete existing volunteer assignments for the collection
        $sqlDelete = "DELETE FROM Volunteer_Collection WHERE id_collecte = ?";
        $stmtDelete = $this->pdo->prepare($sqlDelete);
        if (!$stmtDelete->execute([$collectionId])) {
            throw new PDOException("Erreur lors de la suppression des bénévoles.");
        }
        // Insert new volunteer assignments if provided
        if (!empty($volunteersAssigned)) {
            $sqlInsert = "INSERT INTO Volunteer_Collection (id_collecte, id_benevole) VALUES (?, ?)";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            foreach ($volunteersAssigned as $volunteerId) {
                if (!$stmtInsert->execute([$collectionId, $volunteerId])) {
                    throw new PDOException("Erreur lors de l'assignation des bénévoles.");
                }
            }
        }
    }

    public function updateCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted)
    {
        // Delete existing waste details for the collection
        $sqlDelete = "DELETE FROM Collected_waste WHERE id_collecte = ?";
        $stmtDelete = $this->pdo->prepare($sqlDelete);
        if (!$stmtDelete->execute([$collectionId])) {
            throw new PDOException("Erreur lors de la suppression des déchets collectés.");
        }
        // Insert new waste details if provided
        if (!empty($wasteTypesSubmitted) && !empty($quantitiesSubmitted)) {
            $sqlInsert = "INSERT INTO Collected_waste (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)";
            $stmtInsert = $this->pdo->prepare($sqlInsert);
            for ($i = 0; $i < count($wasteTypesSubmitted); $i++) {
                if (!empty($wasteTypesSubmitted[$i]) && is_numeric($quantitiesSubmitted[$i]) && !$stmtInsert->execute([$collectionId, $wasteTypesSubmitted[$i], $quantitiesSubmitted[$i]])) {
                    throw new PDOException("Erreur lors de l'insertion des déchets collectés.");
                }
            }
        }
    }

    public function deleteCollection($collectionId)
    {
        $sql = "DELETE FROM CollectionEvent WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            throw new PDOException("Erreur lors de la suppression de la collecte.");
        }
    }
}
