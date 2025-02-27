<?php

namespace Kouak\BackOfficeApp\Controllers\CollectedWasteDetails;

use PDO;
use PDOException;
use Kouak\BackOfficeApp\Models\CollectedWasteDetails\CollectedWasteDetailsManager;

class CollectedWasteDetailsController
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var CollectedWasteDetailsManager
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->manager = new CollectedWasteDetailsManager($pdo);
    }

    /**
     * Retrieve a distinct list of waste types.
     *
     * @return array
     *
     * @throws PDOException
     */
    public function getWasteTypesList()
    {
        try {
            return $this->manager->readWasteTypesList();
        } catch (PDOException $e) {
            throw new PDOException("Erreur de la base de données : " . $e->getMessage());
        }
    }

    /**
     * Retrieve the collected waste details for a given collection.
     *
     * @param int $collectionId
     *
     * @return array
     *
     * @throws PDOException
     */
    public function getCollectedWasteDetailsList($collectionId)
    {
        try {
            return $this->manager->readCollectedWasteDetailsList($collectionId);
        } catch (PDOException $e) {
            throw new PDOException("Erreur de base de données : " . $e->getMessage());
        }
    }

    // Optionally, you could add create, update, or delete methods if needed.
}
