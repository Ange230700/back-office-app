<?php

namespace Kouak\BackOfficeApp\Models\Collection;

use PDO;
use Kouak\BackOfficeApp\Models\CollectedWasteDetails\CollectedWasteDetailsManager;
use Kouak\BackOfficeApp\Models\CollectionVolunteer\CollectionVolunteerManager;

class CollectionManager
{
    private $pdo;
    private $collectedWasteDetailsManager;
    private $collectionVolunteerManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->collectedWasteDetailsManager = new CollectedWasteDetailsManager($pdo);
        $this->collectionVolunteerManager = new CollectionVolunteerManager($pdo);
    }

    public function createCollectionWithDetails($submittedDate, $submittedPlace, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted): ?int
    {
        $this->pdo->beginTransaction();
        $collectionId = $this->createCollection($submittedDate, $submittedPlace);
        if ($collectionId === null) {
            $this->pdo->rollBack();
            return null;
        }
        $this->collectionVolunteerManager->createVolunteersCollectionAssignment($collectionId, $volunteersAssigned);
        if (!empty($wasteTypesSubmitted) && !empty($quantitiesSubmitted)) {
            $this->collectedWasteDetailsManager->createCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted);
        }
        $this->pdo->commit();
        return $collectionId;
    }

    public function createCollection($submittedDate, $submittedPlace): ?int
    {
        $sql = "INSERT INTO collectes (date_collecte, lieu) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedDate, $submittedPlace])) {
            return null;
        }
        return $this->pdo->lastInsertId();
    }

    public function readCollection($collectionId): ?array
    {
        $sql = "SELECT id, date_collecte, lieu FROM collectes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readCollectionPlacesList(): ?array
    {
        $sql = "SELECT DISTINCT lieu FROM collectes ORDER BY lieu";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function readCollectionsList(): ?array
    {
        $sql = "SELECT id, CONCAT(DATE_FORMAT(date_collecte, '%d/%m/%Y'), ' - ', lieu) AS collection_label FROM collectes ORDER BY date_collecte";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetchAll();
    }

    public function readDetailedCollectionsList(int $limit, int $offset): ?array
    {
        $sql = "SELECT
                collectes.id,
                collectes.date_collecte,
                collectes.lieu,
                COALESCE(
                       GROUP_CONCAT(DISTINCT benevoles.nom ORDER BY benevoles.nom SEPARATOR ', '),
                       'Aucun bénévole'
                ) AS benevoles,
                COALESCE(
                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            COALESCE(dechets_collectes.type_dechet, 'type (s) non défini(s)'),
                            ' (', ROUND(COALESCE(dechets_collectes.quantite_kg, 0), 1), 'kg)'
                        )
                        ORDER BY dechets_collectes.type_dechet
                        SEPARATOR ', '
                    ),
                    'Aucun déchet collecté'
                ) AS wasteDetails
            FROM collectes
            LEFT JOIN benevoles_collectes
                ON collectes.id = benevoles_collectes.id_collecte
            LEFT JOIN benevoles
                ON benevoles_collectes.id_benevole = benevoles.id
            LEFT JOIN dechets_collectes
                    ON collectes.id = dechets_collectes.id_collecte
            GROUP BY collectes.id
            ORDER BY collectes.date_collecte DESC
            LIMIT ? OFFSET ?;";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$limit, $offset])) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readNumberOfCollections(): ?int
    {
        $sql = "SELECT COUNT(DISTINCT benevoles_collectes.id_collecte) AS total
                FROM benevoles
                INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
                INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function readCollectionsListPaginated(array $paginationParams): ?array
    {
        $limit = $paginationParams['limit'];
        $offset = $paginationParams['offset'];
        $collectionsList = $this->readDetailedCollectionsList($limit, $offset);
        $numberOfCollections = $this->readNumberOfCollections();
        $numberOfPages = ceil($numberOfCollections / $limit);
        return [$collectionsList, $numberOfPages];
    }

    public function readCollectedWastesTotalQuantity(): ?int
    {
        $sql = "SELECT COALESCE(ROUND(SUM(COALESCE(dechets_collectes.quantite_kg,0)),1), 0) AS quantite_total_des_dechets_collectes
                FROM collectes
                LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['quantite_total_des_dechets_collectes'];
    }

    public function readMostRecentCollection(): ?array
    {
        $sql = "SELECT lieu, date_collecte
                FROM collectes
                WHERE date_collecte <= CURDATE()
                ORDER BY date_collecte DESC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readNextCollection(): ?array
    {
        $sql = "SELECT lieu, date_collecte
                FROM collectes
                WHERE date_collecte > CURDATE()
                ORDER BY date_collecte ASC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateVolunteersInCollection($collectionId, $volunteersAssigned): void
    {
        $this->collectionVolunteerManager->updateVolunteersInCollection($collectionId, $volunteersAssigned);
    }

    public function updateCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted): void
    {
        $this->collectedWasteDetailsManager->updateCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted);
    }

    public function updateCollection($submittedDate, $submittedPlace, $collectionId): ?int
    {
        $sql = "UPDATE collectes
                SET date_collecte = COALESCE(?, date_collecte), lieu = COALESCE(?, lieu)
                WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedDate, $submittedPlace, $collectionId])) {
            return null;
        }
        return $stmt->rowCount();
    }

    public function deleteCollection($collectionId): ?int
    {
        $sql = "DELETE FROM collectes WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->rowCount();
    }
}
