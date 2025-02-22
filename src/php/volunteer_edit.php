<?php
session_start();

$isUserLogged = isset($_SESSION["user_id"]);
if (!$isUserLogged) {
    header('Location: login.php');
    exit();
}

$isUserAdmin = $_SESSION["role"] === "admin";
if (!$isUserAdmin) {
    header("Location: volunteer_list.php");
    exit();
}

require 'config.php';


$volunteerListRedirection = "Location: volunteer_list.php";
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header($volunteerListRedirection);
    exit;
}

try {
    $sqlQueryToSelectVolunteerToEdit = "SELECT id, role FROM benevoles WHERE id = ?";
    $statementToGetVolunteerToEdit = $pdo->prepare($sqlQueryToSelectVolunteerToEdit);
    $volunteerToEditId = $_GET['id'];
    if (!$statementToGetVolunteerToEdit->execute([$volunteerToEditId])) {
        die("Erreur lors de la récupération du bénévole.");
    }
    $benevole = $statementToGetVolunteerToEdit->fetch();
    if (!$benevole) {
        header($volunteerListRedirection);
        exit;
    }

    $sqlQueryToSelectCollectionsToAssignToVolunteer = "SELECT id, CONCAT(date_collecte, ' - ', lieu) AS collection_label FROM collectes ORDER BY date_collecte";
    $statementToGetCollectionsToAssignToVolunteer = $pdo->prepare($sqlQueryToSelectCollectionsToAssignToVolunteer);
    if (!$statementToGetCollectionsToAssignToVolunteer->execute()) {
        die("Erreur lors de la récupération des collectes à assigner au bénévole.");
    }
    $collectionsToAssignToVolunteer = $statementToGetCollectionsToAssignToVolunteer->fetchAll();

    $sqlQueryToSelectVolunteerAttendancesArray = "SELECT id_collecte FROM benevoles_collectes WHERE id_benevole = ?";
    $statementToGetVolunteerAttendancesArray = $pdo->prepare($sqlQueryToSelectVolunteerAttendancesArray);
    if (!$statementToGetVolunteerAttendancesArray->execute([$volunteerToEditId])) {
        die("Erreur lors de la récupération des collectes assignées au bénévole.");
    }
    $volunteerAttendancesArray =
        array_column($statementToGetVolunteerAttendancesArray->fetchAll(), 'id_collecte');
} catch (PDOException $pdoException) {
    echo "Erreur de base de données : " . $pdoException->getMessage();
    exit;
}

$isFormSubmitted = $_SERVER["REQUEST_METHOD"] === "POST";
if ($isFormSubmitted) {
    $submittedRole = $_POST["role"];
    $submittedParticipationsArray = $_POST['attendances'];

    try {
        $sqlQueryToUpdateVolunteer = "UPDATE benevoles SET role = COALESCE(?, role) WHERE id = ?";
        $statementToUpdateVolunteer = $pdo->prepare($sqlQueryToUpdateVolunteer);
        if (!$statementToUpdateVolunteer->execute([$submittedRole, $volunteerToEditId])) {
            die("Erreur lors de la mise à jour du rôle du bénévole.");
        }

        $sqlQueryToDeleteVolunteer = "DELETE FROM benevoles_collectes WHERE id_benevole = ?";
        $statementToDeleteVolunteer = $pdo->prepare($sqlQueryToDeleteVolunteer);
        $statementToDeleteVolunteer->execute([$volunteerToEditId]);

        if (isset($submittedParticipationsArray) && is_array($submittedParticipationsArray)) {
            $sqlQueryToInsertVolunteerParticipation = "INSERT INTO benevoles_collectes (id_benevole, id_collecte) VALUES (?, ?)";
            $statementToInsertVolunteerParticipation = $pdo->prepare($sqlQueryToInsertVolunteerParticipation);
            foreach ($submittedParticipationsArray as $id_collecte) {
                if (!$statementToInsertVolunteerParticipation->execute([$volunteerToEditId, $id_collecte])) {
                    die("Erreur lors de l'assignation des collectes au bénévole.");
                }
            }
        }
    } catch (PDOException $pdoException) {
        echo "Erreur de base de données : " . $pdoException->getMessage();
        exit;
    }

    header($volunteerListRedirection);
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require 'headElement.php'; ?>
    <title>Modifier un bénévole</title>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">

        <?php require 'navbar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-cyan-950 mb-6">Modifier un benevole</h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Rôle
                            <select name="role" id="role" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled>Sélectionnez un rôle</option>

                                <option value="participant" <?= ($benevole['role'] === 'participant') ? 'selected' : '' ?>>
                                    Participant
                                </option>

                                <option value="admin" <?= ($benevole['role'] === 'admin') ? 'selected' : '' ?>>
                                    Admin
                                </option>
                            </select>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Participations
                            <?php if (!empty($collectionsToAssignToVolunteer)): ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <?php foreach ($collectionsToAssignToVolunteer as $collection): ?>
                                        <div class="flex items-center mb-2">
                                            <input type="checkbox" name="attendances[]" value="<?= htmlspecialchars($collection['id']) ?>" id="collection_<?= htmlspecialchars($collection['id']) ?>" class="mr-2" <?= in_array($collection['id'], $volunteerAttendancesArray) ? 'checked' : '' ?> />
                                            <label for="collection_<?= htmlspecialchars($collection['id']) ?>" class="text-gray-700">
                                                <?= htmlspecialchars($collection['collection_label']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-gray-500">Aucune collecte n'est disponible pour l'instant.</p>
                                <?php endif; ?>
                        </label>
                    </div>
            </div>

            <?php
            $cancelUrl = "volunteer_list.php";
            $cancelTitle = "Retour à la liste des bénévoles";
            $buttonTitle = "Modifier le bénévole";
            $buttonTextContent = "Modifier le bénévole";
            require 'addEditButtons.php';
            ?>
            </form>
    </div>
    </div>
    </div>

</body>

</html>