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

    $sqlQueryToFetchCollectionsList = "SELECT benevoles_collectes.id_collecte AS id, collectes.date_collecte, collectes.lieu, GROUP_CONCAT(DISTINCT benevoles.nom ORDER BY benevoles.nom SEPARATOR ', ') AS benevoles, GROUP_CONCAT(DISTINCT CONCAT(COALESCE(dechets_collectes.type_dechet, 'type(s) non défini(s)'), ' (', ROUND(COALESCE(dechets_collectes.quantite_kg, 0), 1), 'kg)') ORDER BY dechets_collectes.type_dechet SEPARATOR ', ') AS wasteDetails FROM benevoles INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte GROUP BY benevoles_collectes.id_collecte ORDER BY collectes.date_collecte DESC LIMIT :limit OFFSET :offset";
    $statementToFetchCollectionsList = $pdo->prepare($sqlQueryToFetchCollectionsList);
    $statementToFetchCollectionsList->bindParam(':limit', $limitOfItemsOnOnePage, PDO::PARAM_INT);
    $statementToFetchCollectionsList->bindParam(':offset', $offset, PDO::PARAM_INT);
    $statementToFetchCollectionsList->execute();
    $collectionsList = $statementToFetchCollectionsList->fetchAll();

    $sqlQueryToCountNumberOfRegisteredCollections = "SELECT COUNT(DISTINCT benevoles_collectes.id_collecte) AS total FROM benevoles INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte";
    $statementToGetTotalNumberOfRegisteredCollections = $pdo->query($sqlQueryToCountNumberOfRegisteredCollections);
    $totalOfCollections = $statementToGetTotalNumberOfRegisteredCollections->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalOfCollections / $limitOfItemsOnOnePage);

    $sqlQueryToFetchTotalQuantityOfAllWastesEverCollected = "SELECT ROUND(SUM(COALESCE(dechets_collectes.quantite_kg,0)),1) AS quantite_total_des_dechets_collectes FROM collectes LEFT JOIN dechets_collectes ON collectes.id=dechets_collectes.id_collecte";
    $statementToGetTotalQuantityOfAllWastesEverCollected = $pdo->query($sqlQueryToFetchTotalQuantityOfAllWastesEverCollected);
    $totalQuantityOfAllWastesEverCollected = $statementToGetTotalQuantityOfAllWastesEverCollected->fetch(PDO::FETCH_ASSOC)['quantite_total_des_dechets_collectes'];

    $sqlQueryToFetchMostRecentDoneCollectionDate = "SELECT lieu, date_collecte FROM collectes WHERE date_collecte <= CURDATE() ORDER BY date_collecte DESC LIMIT 1";
    $statementToGetMostRecentDoneCollectionDate = $pdo->query($sqlQueryToFetchMostRecentDoneCollectionDate);
    $mostRecentDoneCollection = $statementToGetMostRecentDoneCollectionDate->fetch();

    $sqlQueryToFetchNextNearestCollectionDate = "SELECT lieu, date_collecte FROM collectes WHERE date_collecte > CURDATE() ORDER BY date_collecte ASC LIMIT 1";
    $statementToGetNextNearestCollectionDate = $pdo->query($sqlQueryToFetchNextNearestCollectionDate);
    $nextNearestCollection = $statementToGetNextNearestCollectionDate->fetch();
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
    <title>Liste des Collectes</title>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <?php require 'navbar.php';
        $dateFormat = "d/m/Y"; ?>

        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-cyan-950 mb-6">Liste des Collectes de Déchets</h1>

            <?php if (isset($_GET['message'])): ?>
                <div id="toast-success-delete" class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-sm dark:text-gray-400 dark:bg-gray-800" role="alert" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 10000;">
                    <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                        </svg>
                        <span class="sr-only">Icône de validation</span>
                    </div>
                    <div class="ms-3 text-sm font-normal"><?= htmlspecialchars($_GET['message']) ?></div>
                    <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" onclick="document.getElementById('toast-success-delete').style.display='none'" aria-label="Fermer">
                        <span class="sr-only">Fermer</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <script>
                    // Optionnel : pour fermer automatiquement le toast après 3 secondes
                    setTimeout(function() {
                        const toast = document.getElementById('toast-success-delete');
                        if (toast) {
                            toast.style.display = 'none';
                        }
                    }, 3000);
                </script>
            <?php endif; ?>

            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des Collectes</h3>
                    <p class="text-3xl font-bold text-blue-600"><?= $totalOfCollections ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des déchets collectés </h3>
                    <p class="text-3xl font-bold text-blue-600"><?= $totalQuantityOfAllWastesEverCollected ?> kg</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Dernière Collecte</h3>
                    <p class="text-lg text-gray-600"><?= htmlspecialchars($mostRecentDoneCollection['lieu']) ?></p>
                    <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($mostRecentDoneCollection['date_collecte'])) ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Prochaine Collecte</h3>
                    <?php if ($nextNearestCollection): ?>
                        <p class="text-lg text-gray-600"><?= htmlspecialchars($nextNearestCollection['lieu']) ?></p>
                        <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($nextNearestCollection['date_collecte'])) ?></p>
                    <?php else: ?>
                        <p class="text-lg text-gray-600">Aucune collecte à venir</p>
                    <?php endif; ?>
                </div>
            </section>

            <div class="overflow-hidden rounded-lg shadow-lg bg-white">
                <table class="w-full table-auto border-collapse">
                    <thead class="bg-cyan-950 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Date</th>
                            <th class="py-3 px-4 text-left">Lieu</th>
                            <th class="py-3 px-4 text-left">Bénévoles</th>
                            <th class="py-3 px-4 text-left">Types de déchets (quantité en kg)</th>
                            <?php if ($_SESSION["role"] !== "admin"): ?>
                            <?php else: ?>
                                <th class="py-3 px-4 text-left">Actions</th>
                            <?php endif ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        <?php foreach ($collectionsList as $collection): ?>
                            <tr class="hover:bg-gray-100 transition duration-200">
                                <td class="py-3 px-4">
                                    <?= date($dateFormat, strtotime($collection['date_collecte'])) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?= htmlspecialchars($collection['lieu']) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?= $collection['benevoles'] ? htmlspecialchars($collection['benevoles']) : 'Aucun bénévole' ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?= htmlspecialchars($collection['wasteDetails']) ?>
                                </td>
                                <?php if ($_SESSION["role"] === "admin"): ?>
                                    <?php
                                    $editUrl = "collection_edit.php?id=" . $collection["id"];
                                    $deleteUrl = "collection_delete.php?id=" . $collection["id"];
                                    $editTitle = "Modifier la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
                                    $deleteTitle = "Supprimer la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
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