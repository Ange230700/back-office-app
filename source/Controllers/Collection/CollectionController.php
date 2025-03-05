<?php

// source\Controllers\Collection\CollectionController.php

namespace Kouak\BackOfficeApp\Controllers\Collection;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\Collection\CollectionManager;
use Kouak\BackOfficeApp\Utilities\Helpers;

class CollectionController
{
    private $pdo;
    private $collectionManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->collectionManager = new CollectionManager($pdo);
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
    public function addNewCollection($submittedDate, $submittedPlace, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted)
    {
        return $this->collectionManager->createCollectionWithDetails($submittedDate, $submittedPlace, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted);
    }

    public function getCollectedWastesTotalQuantity()
    {
        return $this->collectionManager->readCollectedWastesTotalQuantity();
    }

    public function getMostRecentCollection()
    {
        return $this->collectionManager->readMostRecentCollection();
    }

    public function getNextCollection()
    {
        return $this->collectionManager->readNextCollection();
    }

    public function getCollection($collectionId) {
        return $this->collectionManager->readCollection($collectionId);
    }

    public function getVolunteersListWhoAttendedCollection($collectionId) {
        return $this->collectionManager->readVolunteersListWhoAttendedCollection($collectionId);
    }

    public function getPlacesList() {
        return $this->collectionManager->readPlacesList();
    }

    public function getCollectionsList() {
        return $this->collectionManager->readCollectionsList();
    }

    public function getCollectionsListPaginated()
    {
        $paginationParams = Helpers::getPaginationParams();
        return $this->collectionManager->readCollectionsListPaginated($paginationParams);
    }

    public function editCollection($submittedDate, $submittedPlace, $collectionId, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted)
    {
        // Wrap the update operations in a transaction for atomicity.
        $this->pdo->beginTransaction();
        try {
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
        return $this->collectionManager->deleteCollection($collectionId);
    }

}
