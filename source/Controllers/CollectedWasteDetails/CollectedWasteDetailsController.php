<?php

// source\Controllers\CollectedWasteDetails\CollectedWasteDetailsController.php

namespace Kouak\BackOfficeApp\Controllers\CollectedWasteDetails;

use PDO;
use PDOException;
use \Kouak\BackOfficeApp\Errors\DatabaseException;
use Kouak\BackOfficeApp\Models\CollectedWasteDetails\CollectedWasteDetailsManager;

class CollectedWasteDetailsController
{
    private $pdo;
    private $collectedWasteDetailsManager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->collectedWasteDetailsManager = new CollectedWasteDetailsManager($pdo);
    }

    public function getWasteTypesList(): ?array
    {
        try {
            return $this->collectedWasteDetailsManager->readWasteTypesList();
        } catch (PDOException $e) {
            throw new DatabaseException("Une erreur est survenue lors de l'ajout d'une collecte.", 0, $e);
        }
    }

    public function getCollectedWasteDetailsList($collectionId): ?array
    {
        try {
            return $this->collectedWasteDetailsManager->readCollectedWasteDetailsList($collectionId);
        } catch (PDOException $e) {
            throw new DatabaseException("Une erreur est survenue lors de la récupération des déchets collectés.", 0, $e);
        }
    }
}
