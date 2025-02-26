<?php
require 'config.php';

/**
 * Retrieve all volunteers.
 */
function getVolunteersList(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT id, nom FROM benevoles ORDER BY nom");
    if (!$stmt->execute()) {
        die("Erreur lors de la récupération des bénévoles.");
    }
    return $stmt->fetchAll();
}

/**
 * Retrieve distinct waste types.
 */
function getWasteTypesList(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT DISTINCT type_dechet FROM dechets_collectes");
    if (!$stmt->execute()) {
        die("Erreur lors de la récupération des types de déchets.");
    }
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Retrieve distinct places.
 */
function getPlacesList(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT DISTINCT lieu FROM collectes ORDER BY lieu");
    if (!$stmt->execute()) {
        die("Erreur lors de la récupération des lieux.");
    }
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
