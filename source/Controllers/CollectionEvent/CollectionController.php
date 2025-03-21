<?php

// source\Controllers\CollectionEvent\CollectionController.php

namespace Kouak\BackOfficeApp\Controllers\CollectionEvent;

use PDO;
use PDOException;
use \Kouak\BackOfficeApp\Errors\DatabaseException;
use Kouak\BackOfficeApp\Models\CollectionEvent\CollectionManager;
use Kouak\BackOfficeApp\Models\CollectionVolunteer\CollectionVolunteerManager;
use Kouak\BackOfficeApp\Utilities\Helpers;

class CollectionController
{
    private $pdo;
    private $collectionManager;
    private $collectionVolunteerManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->collectionManager = new CollectionManager($pdo);
        $this->collectionVolunteerManager = new CollectionVolunteerManager($pdo);
    }

    public function addNewCollection($submittedDate, $submittedPlace, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted): ?int
    {
        try {
            return $this->collectionManager->createCollectionWithDetails($submittedDate, $submittedPlace, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de l'ajout d'une collecte.", 0, $e);
        }
    }

    public function getCollectedWastesTotalQuantity(): float
    {
        try {
            return $this->collectionManager->readCollectedWastesTotalQuantity();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération du total des déchets collectés.", 0, $e);
        }
    }

    public function getMostRecentCollection(): ?array
    {
        try {
            return $this->collectionManager->readMostRecentCollection();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération de la collecte la plus récente.", 0, $e);
        }
    }

    public function getNextCollection(): ?array
    {
        try {
            return $this->collectionManager->readNextCollection();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération de la collecte suivante.", 0, $e);
        }
    }

    public function getCollection($collectionId): ?array
    {
        try {
            return $this->collectionManager->readCollection($collectionId);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération de la collecte.", 0, $e);
        }
    }

    public function getVolunteersListWhoAttendedCollection($collectionId): ?array
    {
        try {
            return $this->collectionVolunteerManager->readVolunteersListWhoAttendedCollection($collectionId);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération des bénévoles.", 0, $e);
        }
    }

    public function getCollectionPlacesList(): ?array
    {
        try {
            return $this->collectionManager->readCollectionPlacesList();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération des lieux.", 0, $e);
        }
    }

    public function getCollectionsList(): ?array
    {
        try {
            return $this->collectionManager->readCollectionsList();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération des CollectionEvent.", 0, $e);
        }
    }

    public function getCollectionsListPaginated(): ?array
    {
        try {
            $paginationParams = Helpers::getPaginationParams();
            return $this->collectionManager->readCollectionsListPaginated($paginationParams);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération des CollectionEvent.", 0, $e);
        }
    }

    public function getTotalCollections(): ?int
    {
        try {
            return $this->collectionManager->readNumberOfCollections();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la récupération du nombre de CollectionEvent.", 0, $e);
        }
    }


    public function editCollection($submittedDate, $submittedPlace, $collectionId, $volunteersAssigned, $wasteTypesSubmitted, $quantitiesSubmitted): void
    {
        try {
            $this->pdo->beginTransaction();
            $this->collectionManager->updateCollection($submittedDate, $submittedPlace, $collectionId);
            $this->collectionManager->updateVolunteersInCollection($collectionId, $volunteersAssigned);
            $this->collectionManager->updateCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted);
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la mise à jour de la collecte.", 0, $e);
        }
    }

    public function eraseCollection($collectionId): void
    {
        try {
            $this->collectionManager->deleteCollection($collectionId);
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw new DatabaseException("Une erreur est survenue lors de la suppression de la collecte.", 0, $e);
        }
    }
}
