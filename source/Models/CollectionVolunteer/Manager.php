<?php

/* ------------------- CREATE ------------------- */
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
/* ============================================== */

/* ------------------- UPDATE ------------------- */
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