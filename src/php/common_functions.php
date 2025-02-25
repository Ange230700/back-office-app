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
