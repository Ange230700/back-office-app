<?php

// source\Models\CollectionEvent\CollectionManager.php

namespace Kouak\BackOfficeApp\Models\CollectionEvent;

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
        $this->collectionVolunteerManager->createVolunteerCollectionAssignment($collectionId, $volunteersAssigned);
        if (!empty($wasteTypesSubmitted) && !empty($quantitiesSubmitted)) {
            $this->collectedWasteDetailsManager->createCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted);
        }
        $this->pdo->commit();
        return $collectionId;
    }

    public function createCollection($submittedDate, $submittedPlace): ?int
    {
        $sql = "INSERT INTO CollectionEvent (collection_date, collection_place) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedDate, $submittedPlace])) {
            return null;
        }
        return $this->pdo->lastInsertId();
    }

    public function readCollection($collectionId): ?array
    {
        $sql = "SELECT collection_id, collection_date, collection_place FROM CollectionEvent WHERE collection_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function readCollectionPlacesList(): ?array
    {
        $sql = "SELECT DISTINCT collection_place FROM CollectionEvent ORDER BY collection_place";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function readCollectionsList(): ?array
    {
        $sql = "SELECT collection_id, CONCAT(DATE_FORMAT(collection_date, '%d/%m/%Y'), ' - ', collection_place) AS collection_label FROM CollectionEvent ORDER BY collection_date";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetchAll();
    }

    public function readDetailedCollectionsList(int $limit, int $offset): ?array
    {
        $sql = "SELECT
                CollectionEvent.collection_id,
                CollectionEvent.collection_date,
                CollectionEvent.collection_place,
                COALESCE(
                       GROUP_CONCAT(DISTINCT Volunteer.username ORDER BY Volunteer.username SEPARATOR ', '),
                       'Aucun bénévole'
                ) AS Volunteer,
                COALESCE(
                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            COALESCE(Collected_waste.waste_type, 'type(s) non défini(s)'),
                            ' (', REPLACE(FORMAT(COALESCE(Collected_waste.quantity_kg, 0), 1, 'fr_FR'), CHAR(160), ''), 'kg)'
                        )
                        ORDER BY Collected_waste.waste_type
                        SEPARATOR ', '
                    ),
                    'Aucun déchet collecté'
                ) AS wasteDetails
            FROM CollectionEvent
            LEFT JOIN Volunteer_Collection
                ON CollectionEvent.collection_id = Volunteer_Collection.id_collection
            LEFT JOIN Volunteer
                ON Volunteer_Collection.id_volunteer = Volunteer.volunteer_id
            LEFT JOIN Collected_waste
                    ON CollectionEvent.collection_id = Collected_waste.id_collection
            GROUP BY CollectionEvent.collection_id
            ORDER BY CollectionEvent.collection_date DESC
            LIMIT ? OFFSET ?;";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$limit, $offset])) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readNumberOfCollections(): ?int
    {
        $sql = "SELECT COUNT(*) AS total FROM CollectionEvent";
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

    public function readCollectedWastesTotalQuantity(): float
    {
        $sql = "SELECT COALESCE(ROUND(SUM(quantity_kg), 1), 0.0)
                AS collected_waste_total_quantity
                FROM Collected_waste";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return 0.0;
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($result['collected_waste_total_quantity'] ?? 0.0);
    }

    public function readMostRecentCollection(): ?array
    {
        $sql = "SELECT collection_place, collection_date
                FROM CollectionEvent
                WHERE collection_date <= CURDATE()
                ORDER BY collection_date DESC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    public function readNextCollection(): ?array
    {
        $sql = "SELECT collection_place, collection_date
                FROM CollectionEvent
                WHERE collection_date > CURDATE()
                ORDER BY collection_date ASC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
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
        $sql = "UPDATE CollectionEvent
                SET collection_date = COALESCE(?, collection_date), collection_place = COALESCE(?, collection_place)
                WHERE collection_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$submittedDate, $submittedPlace, $collectionId])) {
            return null;
        }
        return $stmt->rowCount();
    }

    public function deleteCollection($collectionId): ?int
    {
        $sql = "DELETE FROM CollectionEvent WHERE collection_id = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->rowCount();
    }
}
