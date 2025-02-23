<?php
require 'common_functions.php';
require 'config.php';

checkUserAdmin();

$collections = getCollections($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedName = $_POST['nom'];
    $submittedEmail = $_POST['email'];
    $submittedPassword = $_POST['mot_de_passe'];
    $hashedPassword = password_hash($submittedPassword, PASSWORD_DEFAULT);
    $submittedRole = $_POST['role'];
    $submittedParticipations = isset($_POST['attendances']) ? $_POST['attendances'] : [];

    try {
        $stmt = $pdo->prepare("INSERT INTO benevoles(nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
        if (!$stmt->execute([$submittedName, $submittedEmail, $hashedPassword, $submittedRole])) {
            die("Erreur lors de l'insertion du bénévole.");
        }
        $volunteerId = $pdo->lastInsertId();

        if (!empty($submittedParticipations)) {
            $stmtParticipation = $pdo->prepare("INSERT INTO benevoles_collectes (id_benevole, id_collecte) VALUES (?, ?)");
            foreach ($submittedParticipations as $collectionId) {
                if (!$stmtParticipation->execute([$volunteerId, $collectionId])) {
                    die("Erreur lors de l'insertion des participations.");
                }
            }
        }
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
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

require 'layout.php';
