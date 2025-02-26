<?php

/* ------------------- CREATE ------------------- */
function createCollection(PDO $pdo, $submittedDate, $submittedPlace)
{
    try {
        $sqlQueryToInsertCollection = "INSERT INTO collectes (date_collecte, lieu) VALUES (?, ?)";
        $statementToAddCollection = $pdo->prepare($sqlQueryToInsertCollection);
        if (!$statementToAddCollection->execute([$submittedDate, $submittedPlace])) {
            die("Erreur lors de l'insertion de la collecte.");
        }
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function assignSeveralVolunteersToCollection(PDO $pdo, $volunteersAssigned, $collectionId)
{
    try {
        $sqlQueryToAssignVolunteerToCollection = "INSERT INTO benevoles_collectes (id_collecte,         id_benevole) VALUES (?, ?)";
        $statementToAssignVolunteerToCollection = $pdo->prepare($sqlQueryToAssignVolunteerToCollection);
        foreach ($volunteersAssigned as $volunteerId) {
            if (!$statementToAssignVolunteerToCollection->execute([$collectionId, $volunteerId])) {
                die("Erreur lors de l'assignation des bénévoles.");
            }
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function addCollectedWastesInformation(PDO $pdo, $wasteTypesSubmitted, $quantitiesSubmitted,        $collectionId)
{
    if (!empty($wasteTypesSubmitted) && !empty($quantitiesSubmitted)) {
        try {
            $stmtWaste = $pdo->prepare("INSERT INTO dechets_collectes (id_collecte, type_dechet,        quantite_kg) VALUES (?, ?, ?)");
            for ($i = 0; $i < count($wasteTypesSubmitted); $i++) {
                if (
                    !empty($wasteTypesSubmitted[$i]) && is_numeric($quantitiesSubmitted[$i]) &&
                    !$stmtWaste->execute([$collectionId, $wasteTypesSubmitted[$i],      $quantitiesSubmitted[$i]])
                ) {
                    die("Erreur lors de l'insertion des déchets collectés.");
                }
            }

            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Erreur de base de données : " . $e->getMessage();
            exit;
        }
    }
}

function addVolunteersToCollection(PDO $pdo, $volunteersAssigned, $collectionId)
{
    try {
        $sqlQueryToInsertVolunteersIntoCollection = "INSERT INTO benevoles_collectes (id_collecte,      id_benevole) VALUES (?, ?)";
        $statementToAddVolunteersToCollection = $pdo->prepare($sqlQueryToInsertVolunteersIntoCollection);
        foreach ($volunteersAssigned as $volunteerId) {
            if (!$statementToAddVolunteersToCollection->execute([$collectionId, $volunteerId])) {
                die("Erreur lors de la mise à jour des bénévoles.");
            }
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}
/* ============================================== */

/* ------------------- READ ------------------- */
function getCollectionsList(PDO $pdo)
{
    try {
        $sqlQueryToSelectCollectionsList = "SELECT id, CONCAT(DATE_FORMAT(date_collecte, '%d/%m/    %Y'), ' - ', lieu) AS collection_label FROM collectes ORDER BY date_collecte";
        $statementToGetCollectionsList = $pdo->prepare($sqlQueryToSelectCollectionsList);
        if (!$statementToGetCollectionsList->execute()) {
            die("Erreur lors de la récupération des collectes.");
        }
        return $statementToGetCollectionsList->fetchAll();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function getCollectedWastesTotalQuantity(PDO $pdo)
{
    try {
        $sqlQueryToSelectCollectedWastesTotalQuantity = "SELECT COALESCE(ROUND(SUM(COALESCE(dechets_collectes.quantite_kg,0)),1), 0)
                    AS quantite_total_des_dechets_collectes
                    FROM collectes
                    LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte";
        $statementToGetCollectedWastesTotalQuantity = $pdo->prepare($sqlQueryToSelectCollectedWastesTotalQuantity);
        if (!$statementToGetCollectedWastesTotalQuantity->execute()) {
            die("Erreur lors de l'exécution de la requête SQL.");
        }
        return $statementToGetCollectedWastesTotalQuantity->fetch(PDO::FETCH_ASSOC)['quantite_total_des_dechets_collectes'];
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function getMostRecentCollection(PDO $pdo)
{
    try {
        $sqlQueryToSelectMostRecentCollection = "SELECT lieu, date_collecte
                        FROM collectes
                        WHERE date_collecte <= CURDATE()
                        ORDER BY date_collecte DESC
                        LIMIT 1";
        $statementToGetMostRecentCollection = $pdo->prepare($sqlQueryToSelectMostRecentCollection);
        if (!$statementToGetMostRecentCollection->execute()) {
            die("Erreur lors de l'exécution de la requête SQL.");
        }
        return $statementToGetMostRecentCollection->fetch();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function getNextCollection(PDO $pdo)
{
    try {
        $sqlQueryToSelectNextCollection = "SELECT lieu, date_collecte
                        FROM collectes
                        WHERE date_collecte > CURDATE()
                        ORDER BY date_collecte ASC
                        LIMIT 1";
        $statementToGetNextCollection = $pdo->prepare($sqlQueryToSelectNextCollection);
        if (!$statementToGetNextCollection->execute()) {
            die("Erreur lors de l'exécution de la requête SQL.");
        }
        return $statementToGetNextCollection->fetch();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function getPlacesList(PDO $pdo)
{
    try {
        $sqlQueryToSelectPlacesList = "SELECT DISTINCT lieu FROM collectes ORDER BY lieu";
        $statementToGetPlacesList = $pdo->prepare($sqlQueryToSelectPlacesList);
        if (!$statementToGetPlacesList->execute()) {
            die("Erreur lors de la récupération des lieux.");
        }
        return $statementToGetPlacesList->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $pdoException) {
        echo "Erreur de la base de données : " . $pdoException->getMessage();
        exit;
    }
}

function getCollectedWasteDetailsList(PDO $pdo, $collectionId)
{
    try {
        $sqlQueryToSelectCollectedWastesDetailsList = "SELECT type_dechet, quantite_kg FROM dechets_collectes WHERE id_collecte = ?";
        $statementToGetCollectedWasteDetailsList = $pdo->prepare($sqlQueryToSelectCollectedWastesDetailsList);
        $statementToGetCollectedWasteDetailsList->execute([$collectionId]);
        return $statementToGetCollectedWasteDetailsList->fetchAll();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function getCollection(PDO $pdo, $collectionId)
{
    try {
        $sqlQueryToSelectCollection = "SELECT id, date_collecte, lieu FROM collectes WHERE id = ?";
        $statementToGetCollection = $pdo->prepare($sqlQueryToSelectCollection);
        if (!$statementToGetCollection->execute([$collectionId])) {
            die("Erreur lors de la récupération de la collecte.");
        }
        return $statementToGetCollection->fetch();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function getVolunteersListWhoAttendedCollection(PDO $pdo, $collectionId)
{
    try {
        $sqlQueryToSelectVolunteersListWhoAttendedCollection = "SELECT id_benevole FROM benevoles_collectes WHERE id_collecte = ?";
        $statementToGetVolunteersListWhoAttendedCollection = $pdo->prepare($sqlQueryToSelectVolunteersListWhoAttendedCollection);
        $statementToGetVolunteersListWhoAttendedCollection->execute([$collectionId]);
        return $statementToGetVolunteersListWhoAttendedCollection->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}
/* ============================================== */

/* ------------------- UPDATE ------------------- */
function updateCollection(PDO $pdo, $submittedDate, $submittedPlace, $collectionId)
{
    try {
        $sqlQueryToUpdateCollection = "UPDATE collectes SET date_collecte = COALESCE(?,     date_collecte), lieu = COALESCE(?, lieu) WHERE id = ?";
        $statementToUpdateCollection = $pdo->prepare($sqlQueryToUpdateCollection);
        if (!$statementToUpdateCollection->execute([$submittedDate, $submittedPlace,    $collectionId])) {
            die("Erreur lors de la mise à jour de la collecte.");
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function updateVolunteersParticipation(PDO $pdo, $collectionId, $volunteersAssigned)
{
    deleteVolunteersFromCollection($pdo, $collectionId);
    addVolunteersToCollection($pdo, $volunteersAssigned, $collectionId);
}

function updateCollectedWasteDetails(PDO $pdo, $collectionId, $wasteTypesSubmitted, $quantitiesSubmitted)
{
    try {
        $sqlQueryToDeleteCollectedWastesDetails = "DELETE FROM dechets_collectes WHERE id_collecte = ?";
        $statementToDeleteCollectedWastesDetails = $pdo->prepare($sqlQueryToDeleteCollectedWastesDetails);
        if (!$statementToDeleteCollectedWastesDetails->execute([$collectionId])) {
            die("Erreur lors de la suppression des déchets collectés.");
        }
        $sqlQueryToInsertNewPotentialCollectedWastesDetails = "INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)";
        $statementToAddNewPotentialCollectedWastesDetails = $pdo->prepare($sqlQueryToInsertNewPotentialCollectedWastesDetails);
        for ($i = 0; $i < count($wasteTypesSubmitted); $i++) {
            if (!empty($wasteTypesSubmitted[$i]) && is_numeric($quantitiesSubmitted[$i]) && !$statementToAddNewPotentialCollectedWastesDetails->execute([$collectionId, $wasteTypesSubmitted[$i], $quantitiesSubmitted[$i]])) {
                die("Erreur lors de l'insertion des déchets collectés.");
            }
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}
/* ============================================== */

/* ------------------- DELETE ------------------- */
function deleteVolunteersFromCollection(PDO $pdo, $collectionId)
{
    try {
        $sqlQueryToDeleteVolunteersFromCollection = "DELETE FROM benevoles_collectes WHERE  id_collecte = ?";
        $statementToDeleteVolunteersFromCollection = $pdo->prepare($sqlQueryToDeleteVolunteersFromCollection);
        if (!$statementToDeleteVolunteersFromCollection->execute([$collectionId])) {
            die("Erreur lors de la suppression des bénévoles.");
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}
/* ============================================== */