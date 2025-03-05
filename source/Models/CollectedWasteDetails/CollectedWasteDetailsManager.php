<?php

// source\Models\CollectedWasteDetails\CollectedWasteDetailsManager.php

namespace Kouak\BackOfficeApp\Models\CollectedWasteDetails;

use PDO;

class CollectedWasteDetailsManager
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted): ?int
    {
        $sql = "INSERT INTO Collected_waste (id_collection, waste_type, quantity_kg) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        for ($i = 0; $i < count($wasteTypesSubmitted); $i++) {
            if (!empty($wasteTypesSubmitted[$i]) && is_numeric($quantitiesSubmitted[$i]) && !$stmt->execute([$collectionId, $wasteTypesSubmitted[$i], $quantitiesSubmitted[$i]])) {
                return null;
            }
        }
        return $this->pdo->lastInsertId();
    }

    public function readWasteTypesList(): ?array
    {
        $sql = "SELECT DISTINCT waste_type FROM Collected_waste";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute()) {
            return null;
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function readCollectedWasteDetailsList($collectionId): ?array
    {
        $sql = "SELECT waste_type, quantity_kg FROM Collected_waste WHERE id_collection = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->fetchAll();
    }

    public function updateCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted): void
    {
        $this->deleteCollectedWasteDetails($collectionId);
        if (!empty($wasteTypesSubmitted) && !empty($quantitiesSubmitted)) {
            $this->createCollectedWasteDetails($collectionId, $wasteTypesSubmitted, $quantitiesSubmitted);
        }
    }

    public function deleteCollectedWasteDetails($collectionId): ?int
    {
        $sql = "DELETE FROM Collected_waste WHERE id_collection = ?";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute([$collectionId])) {
            return null;
        }
        return $stmt->rowCount();
    }
}
