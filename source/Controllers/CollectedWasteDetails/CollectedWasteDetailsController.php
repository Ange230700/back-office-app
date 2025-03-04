<?php

namespace Kouak\BackOfficeApp\Controllers\CollectedWasteDetails;

use PDO;
use PDOException;
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
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }

    public function getCollectedWasteDetailsList($collectionId): ?array
    {
        try {
            return $this->collectedWasteDetailsManager->readCollectedWasteDetailsList($collectionId);
        } catch (PDOException $e) {
            throw new PDOException("Erreur de base de données : " . $e->getMessage());
        }
    }
}
