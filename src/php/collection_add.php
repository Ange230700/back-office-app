<?php
require 'common_functions.php';
require 'collection_functions.php';
require 'config.php';

checkUserAdmin();

try {
    $volunteers = getVolunteers($pdo);
    $wasteTypes = getWasteTypes($pdo);
    $places = getPlaces($pdo);
} catch (PDOException $pdoException) {
    echo "Erreur de la base de données : " . $pdoException->getMessage();
}

$collection = [];
$selectedVolunteers = [];
$collectedWastes = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedDate = $_POST["date"];
    $submittedPlace = $_POST["lieu"];
    $volunteersAssigned = isset($_POST["benevoles"]) ? $_POST["benevoles"] : [];
    $wasteTypesSubmitted = isset($_POST['type_dechet']) ? $_POST['type_dechet'] : [];
    $quantitiesSubmitted = isset($_POST['quantite_kg']) ? $_POST['quantite_kg'] : [];

    try {
        $stmt = $pdo->prepare("INSERT INTO collectes (date_collecte, lieu) VALUES (?, ?)");
        if (!$stmt->execute([$submittedDate, $submittedPlace])) {
            die("Erreur lors de l'insertion de la collecte.");
        }
        $collectionId = $pdo->lastInsertId();

        $stmtVolunteer = $pdo->prepare("INSERT INTO benevoles_collectes (id_collecte, id_benevole) VALUES (?, ?)");
        foreach ($volunteersAssigned as $volId) {
            if (!$stmtVolunteer->execute([$collectionId, $volId])) {
                die("Erreur lors de l'assignation des bénévoles.");
            }
        }

        if (!empty($wasteTypesSubmitted) && !empty($quantitiesSubmitted)) {
            $stmtWaste = $pdo->prepare("INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)");
            for ($i = 0; $i < count($wasteTypesSubmitted); $i++) {
                if (
                    !empty($wasteTypesSubmitted[$i]) && is_numeric($quantitiesSubmitted[$i]) &&
                    !$stmtWaste->execute([$collectionId, $wasteTypesSubmitted[$i], $quantitiesSubmitted[$i]])
                ) {
                    die("Erreur lors de l'insertion des déchets collectés.");
                }
            }
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
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
require 'collection_form.php';
$content = ob_get_clean();

require 'layout.php';
