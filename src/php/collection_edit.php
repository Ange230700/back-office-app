<?php
require 'common_functions.php';
require 'config.php';

checkUserAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: collection_list.php");
    exit;
}
$collectionId = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT id, date_collecte, lieu FROM collectes WHERE id = ?");
    $stmt->execute([$collectionId]);
    $collection = $stmt->fetch();
    if (!$collection) {
        header("Location: collection_list.php");
        exit;
    }
    $stmt = $pdo->prepare("SELECT id_benevole FROM benevoles_collectes WHERE id_collecte = ?");
    $stmt->execute([$collectionId]);
    $selectedVolunteers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $volunteers = getVolunteers($pdo);
    $wasteTypes = getWasteTypes($pdo);
    $stmt = $pdo->prepare("SELECT type_dechet, quantite_kg FROM dechets_collectes WHERE id_collecte = ?");
    $stmt->execute([$collectionId]);
    $collectedWastes = $stmt->fetchAll();
    if (empty($collectedWastes)) {
        $collectedWastes[] = ['type_dechet' => '', 'quantite_kg' => ''];
    }
} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedDate = $_POST["date"];
    $submittedPlace = $_POST["lieu"];
    $volunteersAssigned = isset($_POST["benevoles"]) ? $_POST["benevoles"] : [];
    $wasteTypesSubmitted = isset($_POST['type_dechet']) ? $_POST['type_dechet'] : [];
    $quantitiesSubmitted = isset($_POST['quantite_kg']) ? $_POST['quantite_kg'] : [];

    try {
        $stmt = $pdo->prepare("UPDATE collectes SET date_collecte = COALESCE(?, date_collecte), lieu = COALESCE(?, lieu) WHERE id = ?");
        if (!$stmt->execute([$submittedDate, $submittedPlace, $collectionId])) {
            die("Erreur lors de la mise à jour de la collecte.");
        }
        $pdo->prepare("DELETE FROM benevoles_collectes WHERE id_collecte = ?")->execute([$collectionId]);
        $stmtVolunteer = $pdo->prepare("INSERT INTO benevoles_collectes (id_collecte, id_benevole) VALUES (?, ?)");
        foreach ($volunteersAssigned as $volId) {
            if (!$stmtVolunteer->execute([$collectionId, $volId])) {
                die("Erreur lors de la mise à jour des bénévoles.");
            }
        }
        $pdo->prepare("DELETE FROM dechets_collectes WHERE id_collecte = ?")->execute([$collectionId]);
        $stmtWaste = $pdo->prepare("INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)");
        for ($i = 0; $i < count($wasteTypesSubmitted); $i++) {
            if (!empty($wasteTypesSubmitted[$i]) && is_numeric($quantitiesSubmitted[$i])) {
                $stmtWaste->execute([$collectionId, $wasteTypesSubmitted[$i], $quantitiesSubmitted[$i]]);
            }
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
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
require 'collection_form.php';
$content = ob_get_clean();

require 'layout.php';
