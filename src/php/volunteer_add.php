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

require "config.php";

try {
    $sqlQueryToSelectCollectionsArray = "SELECT id, CONCAT(date_collecte, ' - ', lieu) AS collection_label FROM collectes ORDER BY date_collecte";
    $statementToGetCollectionsArray = $pdo->prepare($sqlQueryToSelectCollectionsArray);
    if (!$statementToGetCollectionsArray->execute()) {
        die("Erreur lors de la récupération des collectes.");
    }
    $collectionsArray = $statementToGetCollectionsArray->fetchAll();
} catch (PDOException $pdoException) {
    echo "Erreur de base de données : " . $pdoException->getMessage();
    exit;
}

$isFormSubmitted = $_SERVER["REQUEST_METHOD"] === "POST";
if ($isFormSubmitted) {
    $submittedName = $_POST['nom'];
    $submittedEmail = $_POST['email'];
    $submittedPassword = $_POST['mot_de_passe'];
    $hashedPassword = password_hash($submittedPassword, PASSWORD_DEFAULT);
    $submittedRole = $_POST['role'];
    $submittedParticipationsArray = isset($_POST['attendances']) ? $_POST['attendances'] : [];

    try {
        $sqlQueryToInsertVolunteer = "INSERT INTO benevoles(nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)";
        $statementToAddVolunteer = $pdo->prepare($sqlQueryToInsertVolunteer);
        if (!$statementToAddVolunteer->execute([$submittedName, $submittedEmail, $hashedPassword, $submittedRole])) {
            die("Erreur lors de l'insertion du bénévole dans la base de données.");
        }
        $volunteerId = $pdo->lastInsertId();

        if (isset($submittedParticipationsArray)) {
            $sqlQueryToInsertCollectionsVolunteerAssignment = "INSERT INTO benevoles_collectes (id_benevole, id_collecte) VALUES (?, ?)";
            $statementToAddCollectionsVolunteerAssignment = $pdo->prepare($sqlQueryToInsertCollectionsVolunteerAssignment);
            foreach ($submittedParticipationsArray as $collectionId) {
                if (!$statementToAddCollectionsVolunteerAssignment->execute([$volunteerId, $collectionId])) {
                    die("Erreur lors de l'insertion des participations dans la base de données.");
                }
            }
        }
    } catch (PDOException $pdoException) {
        echo "Erreur de base de données : " . $pdoException->getMessage();
        exit;
    }

    header("Location: volunteer_list.php");
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
    <title>Ajouter un bénévole</title>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <?php require 'navbar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-cyan-950 mb-6">Ajouter un Bénévole</h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-700">
                            Nom
                            <input type="text" id="nom" name="nom"
                                class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Nom du bénévole" required />
                        </label>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                            <input type="email" id="email" name="email"
                                class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Email du bénévole" required />
                        </label>
                    </div>

                    <div>
                        <label for="mot_de_passe" class="block text-sm font-medium text-gray-700">
                            Mot de passe
                            <input type="password" id="mot_de_passe" name="mot_de_passe"
                                class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Mot de passe" required />
                        </label>
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Rôle
                            <select id="role" name="role"
                                class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="participant">Participant</option>
                                <option value="admin">Admin</option>
                            </select>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Participations
                            <?php if (!empty($collectionsArray)): ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <?php foreach ($collectionsArray as $collection): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="attendances[]" value="<?= $collection['id'] ?>" id="collection_<?= $collection['id'] ?>" class="mr-2">
                                            <label for="collection_<?= $collection['id'] ?>" class="text-gray-700">
                                                <?= $collection['collection_label'] ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500">Aucune collecte n'est disponible pour l'instant.</p>
                            <?php endif; ?>
                        </label>
                    </div>

                    <?php
                    $cancelUrl = "volunteer_list.php";
                    $cancelTitle = "Retour à la liste des bénévoles";
                    $buttonTitle = "Ajouter le bénévole";
                    $buttonTextContent = "Ajouter le bénévole";
                    require 'addEditButtons.php';
                    ?>
                </form>
            </div>
    </div>
    </div>

</body>

</html>