<?php

namespace Kouak\BackOfficeApp\Models\CollectedWasteDetails;

use PDO;
use PDOException;

class CollectedWasteDetailsManager
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * Constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create new collected waste details for a given collection.
     *
     * @param int   $collectionId
     * @param array $wasteTypesSubmitted
     * @param array $quantitiesSubmitted
     *
     * @return int Last inserted ID (if any)
     *
     * @throws PDOException
     */
    public function createNewPotentialCollectedWastesDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted)
    {
        $sql = "INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        for ($i = 0; $i < count($wasteTypesSubmitted); $i++) {
            if (!empty($wasteTypesSubmitted[$i]) && is_numeric($quantitiesSubmitted[$i])) {
                if (!$stmt->execute([$collectionId, $wasteTypesSubmitted[$i], $quantitiesSubmitted[$i]])) {
                    throw new PDOException("Erreur lors de l'insertion des déchets collectés.");
                }
            }
        }
        return $this->pdo->lastInsertId();
    }

    /**
     * Get a distinct list of waste types.
     *
     * @return array
     *
     * @throws PDOException
     */
    public function readWasteTypesList()
    {
        $sql = "SELECT DISTINCT type_dechet FROM dechets_collectes";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            throw new PDOException("Erreur lors de la récupération des types de déchets.");
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get collected waste details for a given collection.
     *
     * @param int $collectionId
     *
     * @return array
     *
     * @throws PDOException
     */
    public function readCollectedWasteDetailsList($collectionId)
    {
        $sql = "SELECT type_dechet, quantite_kg FROM dechets_collectes WHERE id_collecte = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            throw new PDOException("Erreur lors de la récupération des déchets collectés.");
        }
        return $stmt->fetchAll();
    }

    /**
     * Delete all collected waste details for a given collection.
     *
     * @param int $collectionId
     *
     * @throws PDOException
     */
    public function deleteCollectedWastesDetails($collectionId)
    {
        $sql = "DELETE FROM dechets_collectes WHERE id_collecte = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            throw new PDOException("Erreur lors de la suppression des déchets collectés.");
        }
    }
}
