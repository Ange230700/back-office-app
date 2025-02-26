<?php
require 'init.php';

checkUserAdmin();

$volunteersList = getVolunteersList($pdo);
$wasteTypesList = getWasteTypesList($pdo);
$placesList = getPlacesList($pdo);

$collection = [];
$selectedVolunteersList = [];
$collectedWastesList = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedDate = $_POST["date"];
    $submittedPlace = $_POST["lieu"];
    $volunteersAssigned = isset($_POST["benevoles"]) ? $_POST["benevoles"] : [];
    $wasteTypesSubmitted = isset($_POST['type_dechet']) ? $_POST['type_dechet'] : [];
    $quantitiesSubmitted = isset($_POST['quantite_kg']) ? $_POST['quantite_kg'] : [];

    $collectionId = createCollection($pdo, $submittedDate, $submittedPlace);
    assignSeveralVolunteersToCollection($pdo, $volunteersAssigned, $collectionId);
    addCollectedWastesInformation($pdo, $wasteTypesSubmitted, $quantitiesSubmitted, $collectionId);
    header("Location: collection_list.php");
    exit;
}

$pageTitle = "Ajouter une collecte";
$pageHeader = "Ajouter une collecte";

$actionUrl = $_SERVER['PHP_SELF'];
$cancelUrl = "collection_list.php";
$cancelTitle = "Retour à la liste des collectes";
$buttonTitle = "Ajouter la collecte";
$buttonTextContent = "Ajouter la collecte";

ob_start();
require 'collectionFormComponent.php';
$content = ob_get_clean();

require 'layoutPage.php';
