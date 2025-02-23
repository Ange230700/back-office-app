<?php
session_start();

function checkUserAdmin()
{
    if (!isset($_SESSION["user_id"])) {
        header('Location: login.php');
        exit();
    }
    if ($_SESSION["role"] !== "admin") {
        header("Location: volunteer_list.php");
        exit();
    }
}

function getCollections(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT id, CONCAT(DATE_FORMAT(date_collecte, '%d/%m/%Y'), ' - ', lieu) AS collection_label FROM collectes ORDER BY date_collecte");
    if (!$stmt->execute()) {
        die("Erreur lors de la récupération des collectes.");
    }
    return $stmt->fetchAll();
}
