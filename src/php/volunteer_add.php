<?php
require 'init.php';

checkUserAdmin();

$collectionsList = getCollectionsList($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedName = $_POST['nom'];
    $submittedEmail = $_POST['email'];
    $submittedPassword = $_POST['mot_de_passe'];
    $hashedPassword = password_hash($submittedPassword, PASSWORD_DEFAULT);
    $submittedRole = $_POST['role'];
    $submittedParticipations = isset($_POST['attendances']) ? $_POST['attendances'] : [];

    $volunteerId = addVolunteer($pdo, $submittedName, $submittedEmail, $hashedPassword, $submittedRole);
    addVolunteerParticipation($pdo, $submittedParticipations, $volunteerId);
    header("Location: volunteer_list.php");
    exit;
}

$isEdit = false;
$selectedCollections = [];
$cancelUrl = "volunteer_list.php";
$cancelTitle = "Retour à la liste des bénévoles";
$buttonTitle = "Ajouter le bénévole";
$buttonTextContent = "Ajouter le bénévole";

$pageTitle = "Ajouter un bénévole";
$pageHeader = "Ajouter un Bénévole";

ob_start();
require 'volunteer_form.php';
$content = ob_get_clean();

require 'layoutPage.php';
