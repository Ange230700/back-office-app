<?php
require 'config.php';

function checkUserAdmin()
{
    session_start();
    if (!isset($_SESSION["user_id"])) {
        header('Location: login.php');
        exit();
    }
    if ($_SESSION["role"] !== "admin") {
        header("Location: collection_list.php");
        exit();
    }
}

function getVolunteers(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT id, nom FROM benevoles ORDER BY nom");
    if (!$stmt->execute()) {
        die("Erreur lors de la récupération des bénévoles.");
    }
    return $stmt->fetchAll();
}

function getWasteTypes(PDO $pdo)
{
    $stmt = $pdo->prepare("SELECT DISTINCT type_dechet FROM dechets_collectes");
    if (!$stmt->execute()) {
        die("Erreur lors de la récupération des types de déchets.");
    }
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
