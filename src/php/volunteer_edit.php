<?php
require 'init.php';

checkUserAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: volunteer_list.php");
    exit;
}

$volunteerId = $_GET['id'];
$volunteer = getEditableFieldsOfVolunteer($pdo, $volunteerId);
if (!$volunteer) {
    header("Location: volunteer_list.php");
    exit;
}

$collectionsList = getCollectionsList($pdo);
$selectedCollections = getCollectionsListVolunteerAttended($pdo, $volunteerId);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedRole = $_POST["role"];
    $submittedParticipations = isset($_POST['attendances']) ? $_POST['attendances'] : [];

    updateVolunteer($pdo, $submittedRole, $volunteerId);
    updateVolunteerParticipations($pdo, $volunteerId, $submittedParticipations);
    header("Location: volunteer_list.php");
    exit;
}

$isEdit = true;
$cancelUrl = "volunteer_list.php";
$cancelTitle = "Retour à la liste des bénévoles";
$buttonTitle = "Modifier le bénévole";
$buttonTextContent = "Modifier le bénévole";

$pageTitle = "Modifier un bénévole";
$pageHeader = "Modifier un Bénévole";

ob_start();
require 'volunteer_form.php';
$content = ob_get_clean();

require 'layoutPage.php';
