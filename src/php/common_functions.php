<?php
session_start();

function checkUserLoggedIn()
{
    if (!isset($_SESSION["user_id"])) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Check that the current user is an admin.
 * Redirects to login if not authenticated or to a generic page if not an admin.
 */
function checkUserAdmin()
{
    checkUserLoggedIn();

    if ($_SESSION["role"] !== "admin") {
        // Change this to a generic page or error page if needed
        header("Location: index.php");
        exit();
    }
}

/**
 * Retrieve all collections.
 */
function getCollections(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT id, CONCAT(DATE_FORMAT(date_collecte, '%d/%m/%Y'), ' - ', lieu) AS collection_label FROM collectes ORDER BY date_collecte");
    if (!$stmt->execute()) {
        die("Erreur lors de la récupération des collectes.");
    }
    return $stmt->fetchAll();
}

/**
 * Retrieve all volunteers.
 */
function getVolunteers(PDO $pdo)
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
function getWasteTypes(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT DISTINCT type_dechet FROM dechets_collectes");
    if (!$stmt->execute()) {
        die("Erreur lors de la récupération des types de déchets.");
    }
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
