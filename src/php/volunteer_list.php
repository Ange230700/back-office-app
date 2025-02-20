<?php
session_start();

$isUserLoggedIn = isset($_SESSION["user_id"]);

if (!$isUserLoggedIn) {
    header('Location: login.php');
    exit();
}

require 'config.php';

try {
    $limitOfItemsOnOnePage = 3;
    $pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
    $offset = ($pageNumber - 1) * $limitOfItemsOnOnePage;

    $sqlQueryToFetchVolunteersList = "SELECT benevoles.id, benevoles.nom, benevoles.email, benevoles.role, COALESCE(GROUP_CONCAT(CONCAT(collectes.lieu, ' (', collectes.date_collecte, ')') SEPARATOR ', '), 'Aucune participation pour le moment') AS 'participations' FROM benevoles LEFT JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole LEFT JOIN collectes ON collectes.id = benevoles_collectes.id_collecte GROUP BY benevoles.id ORDER BY benevoles.nom ASC LIMIT :limit OFFSET :offset";
    $statementToFetchVolunteersList = $pdo->prepare($sqlQueryToFetchVolunteersList);
    $statementToFetchVolunteersList->bindParam(':limit', $limitOfItemsOnOnePage, PDO::PARAM_INT);
    $statementToFetchVolunteersList->bindParam(':offset', $offset, PDO::PARAM_INT);
    $statementToFetchVolunteersList->execute();
    $volunteersList = $statementToFetchVolunteersList->fetchAll();

    $sqlQueryToCountNumberOfRegisteredVolunteers = "SELECT COUNT(*) AS total FROM benevoles";
    $statementToGetTotalNumberOfRegisteredVolunteers = $pdo->query($sqlQueryToCountNumberOfRegisteredVolunteers);
    $totalOfVolunteers = $statementToGetTotalNumberOfRegisteredVolunteers->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalOfVolunteers / $limitOfItemsOnOnePage);
} catch (PDOException $pdoException) {
    echo "Erreur de base de données : " . $pdoException->getMessage();
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
    <title>Liste des Bénévoles</title>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <?php require 'navbar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl text-cyan-950 font-bold mb-6">Liste des Bénévoles</h1>

            <div class="overflow-hidden rounded-lg shadow-lg bg-white">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-cyan-950 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Nom</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">Rôle</th>
                            <th class="py-3 px-4 text-left">Participations</th>
                            <?php if ($_SESSION["role"] !== "admin"): ?>
                            <?php else: ?>
                                <th class="py-3 px-4 text-left">Actions</th>
                            <?php endif ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        <?php foreach ($volunteersList as $volunteer): ?>
                            <tr class="hover:bg-gray-100 transition duration-200">
                                <td class="py-3 px-4">
                                    <?= htmlspecialchars($volunteer["nom"]) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?= htmlspecialchars($volunteer["email"]) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?= htmlspecialchars($volunteer["role"]) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?= htmlspecialchars($volunteer["participations"]) ? htmlspecialchars($volunteer["participations"]) : "Aucune" ?>
                                </td>
                                <?php if ($_SESSION["role"] === "admin"): ?>
                                    <?php
                                    $editUrl = "volunteer_edit.php?id=" . $volunteer["id"];
                                    $deleteUrl = "volunteer_delete.php?id=" . $volunteer["id"];
                                    $editTitle = "Modifier " . htmlspecialchars($volunteer["nom"]);
                                    $deleteTitle = "Supprimer " . htmlspecialchars($volunteer["nom"]);
                                    require 'actionsButtons.php';
                                    ?>
                                <?php endif ?>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php require 'paginationButtons.php'; ?>
        </main>
    </div>
</body>

</html>