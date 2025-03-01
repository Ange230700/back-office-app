<?php

namespace Kouak\BackOfficeApp\Controllers\Collection;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\Collection\CollectionManager;
use Kouak\BackOfficeApp\Models\CollectionVolunteer\CollectionVolunteerManager;
use Kouak\BackOfficeApp\Utilities\Helpers;

class CollectionController
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var CollectionManager
     */
    private $collectionManager;

    /**
     * @var CollectionVolunteerManager
     */
    private $collectionVolunteerManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->collectionManager = new CollectionManager($pdo);
        $this->collectionVolunteerManager = new CollectionVolunteerManager($pdo);
    }

    /**
     * Add a new collection with its associated volunteers and waste details.
     *
     * @param string $submittedDate
     * @param string $submittedPlace
     * @param array  $volunteersAssigned
     * @param array  $wasteTypesSubmitted
     * @param array  $quantitiesSubmitted
     *
     * @return int The ID of the newly created collection.
     */
    public function addNewCollection($submittedDate, $submittedPlace, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted): int
    {
        try {
            return $this->collectionManager->createCollectionWithDetails($submittedDate, $submittedPlace, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getCollectedWastesTotalQuantity()
    {
        try {
            return $this->collectionManager->readCollectedWastesTotalQuantity();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getMostRecentCollection()
    {
        try {
            return $this->collectionManager->readMostRecentCollection();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getNextCollection()
    {
        try {
            return $this->collectionManager->readNextCollection();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getCollection($collectionId)
    {
        try {
            return $this->collectionManager->readCollection($collectionId);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getVolunteersListWhoAttendedCollection($collectionId)
    {
        try {
            return $this->collectionVolunteerManager->readVolunteersListWhoAttendedCollection($collectionId);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getPlacesList()
    {
        try {
            return $this->collectionManager->readPlacesList();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getCollectionsList()
    {
        try {
            return $this->collectionManager->readCollectionsList();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function getCollectionsListPaginated()
    {
        try {
            $paginationParams = Helpers::getPaginationParams();
            return $this->collectionManager->readCollectionsListPaginated($paginationParams);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function editCollection($submittedDate, $submittedPlace, $collectionId, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted)
    {
        try {
            $this->pdo->beginTransaction();
            $this->collectionManager->updateCollection($submittedDate, $submittedPlace, $collectionId);
            $this->collectionManager->updateVolunteersParticipation($collectionId, $volunteersAssigned);
            $this->collectionManager->updateCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted);
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function eraseCollection($collectionId)
    {
        try {
            $this->collectionManager->deleteCollection($collectionId);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
