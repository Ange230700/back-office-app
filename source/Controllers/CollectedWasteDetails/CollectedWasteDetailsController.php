<?php

namespace Kouak\BackOfficeApp\Controllers\CollectedWasteDetails;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\CollectedWasteDetails\CollectedWasteDetailsManager;

class CollectedWasteDetailsController
{
    private $pdo;
    private $manager;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->manager = new CollectedWasteDetailsManager($pdo);
    }

    public function getWasteTypesList()
    {
        try {
            return $this->manager->readWasteTypesList();
        } catch (PDOException $e) {
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }

    public function getCollectedWasteDetailsList($collectionId)
    {
        try {
            return $this->manager->readCollectedWasteDetailsList($collectionId);
        } catch (PDOException $e) {
            throw new PDOException("Erreur de base de données : " . $e->getMessage());
        }
    }
}
