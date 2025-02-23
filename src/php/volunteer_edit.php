<?php
require 'common_functions.php';
require 'config.php';

checkUserAdmin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: volunteer_list.php");
    exit;
}
$volunteerId = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT id, role FROM benevoles WHERE id = ?");
    if (!$stmt->execute([$volunteerId])) {
        die("Erreur lors de la récupération du bénévole.");
    }
    $volunteer = $stmt->fetch();
    if (!$volunteer) {
        header("Location: volunteer_list.php");
        exit;
    }
    $collections = getCollections($pdo);
    $stmt = $pdo->prepare("SELECT id_collecte FROM benevoles_collectes WHERE id_benevole = ?");
    $stmt->execute([$volunteerId]);
    $selectedCollections = array_column($stmt->fetchAll(), 'id_collecte');
} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedRole = $_POST["role"];
    $submittedParticipations = isset($_POST['attendances']) ? $_POST['attendances'] : [];

    try {
        $stmt = $pdo->prepare("UPDATE benevoles SET role = COALESCE(?, role) WHERE id = ?");
        if (!$stmt->execute([$submittedRole, $volunteerId])) {
            die("Erreur lors de la mise à jour du rôle.");
        }
        $pdo->prepare("DELETE FROM benevoles_collectes WHERE id_benevole = ?")->execute([$volunteerId]);
        if (!empty($submittedParticipations)) {
            $stmtParticipation = $pdo->prepare("INSERT INTO benevoles_collectes (id_benevole, id_collecte) VALUES (?, ?)");
            foreach ($submittedParticipations as $collectionId) {
                if (!$stmtParticipation->execute([$volunteerId, $collectionId])) {
                    die("Erreur lors de l'assignation des collectes.");
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

require 'layout.php';
