<?php

/* ------------------- CREATE ------------------- */
/* ============================================== */

/* ------------------- READ ------------------- */
function getWasteTypesList(PDO $pdo)
{
    try {
        $sqlQueryToSelectWasteTypesList = "SELECT DISTINCT type_dechet FROM dechets_collectes";
        $statementToGetWasteTypesList = $pdo->prepare($sqlQueryToSelectWasteTypesList);
        if (!$statementToGetWasteTypesList->execute()) {
            die("Erreur lors de la récupération des types de déchets.");
        }
        return $statementToGetWasteTypesList->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $pdoException) {
        echo "Erreur de la base de données : " . $pdoException->getMessage();
        exit;
    }
}
/* ============================================== */

/* ------------------- UPDATE ------------------- */
/* ============================================== */

/* ------------------- DELETE ------------------- */
/* ============================================== */