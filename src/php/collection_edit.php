<?php
require 'init.php';

checkUserAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: collection_list.php");
    exit;
}

$collectionId = $_GET['id'];
$collection = getCollection($pdo, $collectionId);
if (!$collection) {
    header("Location: collection_list.php");
    exit;
}

$selectedVolunteersList = getVolunteersListWhoAttendedCollection($pdo, $collectionId);
$volunteersList = getVolunteersList($pdo);
$wasteTypesList = getWasteTypesList($pdo);
$placesList = getPlacesList($pdo);
$collectedWasteDetailsList = getCollectedWasteDetailsList($pdo, $collectionId);
if (empty($collectedWasteDetailsList)) {
    $collectedWasteDetailsList[] = ['type_dechet' => '', 'quantite_kg' => ''];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedDate = $_POST["date"];
    $submittedPlace = $_POST["lieu"];
    $volunteersAssigned = isset($_POST["benevoles"]) ? $_POST["benevoles"] : [];
    $wasteTypesSubmitted = isset($_POST['type_dechet']) ? $_POST['type_dechet'] : [];
    $quantitiesSubmitted = isset($_POST['quantite_kg']) ? $_POST['quantite_kg'] : [];

    updateCollection($pdo, $submittedDate, $submittedPlace, $collectionId);
    updateVolunteersParticipation($pdo, $collectionId, $volunteersAssigned);
    updateCollectedWasteDetails($pdo, $collectionId, $wasteTypesSubmitted, $quantitiesSubmitted);
    header("Location: collection_list.php");
    exit;
}

$pageTitle = "Modifier une collecte";
$pageHeader = "Modifier une collecte";

$actionUrl = $_SERVER['PHP_SELF'] . "?id=" . urlencode($collectionId);
$cancelUrl = "collection_list.php";
$cancelTitle = "Retour à la liste des collectes";
$buttonTitle = "Modifier la collecte";
$buttonTextContent = "Modifier la collecte";

ob_start();
require 'collectionFormComponent.php';
$content = ob_get_clean();

require 'layoutPage.php';
