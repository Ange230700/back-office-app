<?php
require 'init.php';

// Pagination parameters
$limitOfItemsOnOnePage = 3;
$pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
$offset = ($pageNumber - 1) * $limitOfItemsOnOnePage;

// Query to fetch collections with details
$sql = "SELECT benevoles_collectes.id_collecte AS id, collectes.date_collecte, collectes.lieu, GROUP_CONCAT(DISTINCT benevoles.nom ORDER BY benevoles.nom SEPARATOR ', ') AS benevoles, GROUP_CONCAT(DISTINCT CONCAT(COALESCE(dechets_collectes.type_dechet, 'type(s) non défini(s)'), ' (', ROUND(COALESCE(dechets_collectes.quantite_kg, 0), 1), 'kg)')
    ORDER BY dechets_collectes.type_dechet SEPARATOR ', ') AS wasteDetails
    FROM benevoles
    INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
    INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte
    LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte
    GROUP BY benevoles_collectes.id_collecte
    ORDER BY collectes.date_collecte DESC
    LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':limit', $limitOfItemsOnOnePage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$collectionsList = $stmt->fetchAll();

// Get total number of collections for pagination
$countSql = "SELECT COUNT(DISTINCT benevoles_collectes.id_collecte) AS total
             FROM benevoles
             INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
             INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte";
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute();
$totalCollections = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalCollections / $limitOfItemsOnOnePage);

// Additional statistics queries
$totalWasteSql = "SELECT ROUND(SUM(COALESCE(dechets_collectes.quantite_kg,0)),1) AS quantite_total_des_dechets_collectes
                  FROM collectes
                  LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte";
$totalWasteStmt = $pdo->query($totalWasteSql);
$totalWaste = $totalWasteStmt->fetch(PDO::FETCH_ASSOC)['quantite_total_des_dechets_collectes'];

$recentCollectionSql = "SELECT lieu, date_collecte FROM collectes
                        WHERE date_collecte <= CURDATE()
                        ORDER BY date_collecte DESC
                        LIMIT 1";
$recentStmt = $pdo->query($recentCollectionSql);
$mostRecentCollection = $recentStmt->fetch();

$nextCollectionSql = "SELECT lieu, date_collecte FROM collectes
                      WHERE date_collecte > CURDATE()
                      ORDER BY date_collecte ASC
                      LIMIT 1";
$nextStmt = $pdo->query($nextCollectionSql);
$nextCollection = $nextStmt->fetch();

$dateFormat = "d/m/Y";

$pageTitle = "Liste des Collectes";
$pageHeader = "Liste des Collectes de Déchets";

ob_start();
?>
<!-- Statistics Section -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des Collectes</h3>
        <p class="text-3xl font-bold text-blue-600"><?= $totalCollections ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des déchets collectés</h3>
        <p class="text-3xl font-bold text-blue-600"><?= $totalWaste ?> kg</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Dernière Collecte</h3>
        <p class="text-lg text-gray-600"><?= htmlspecialchars($mostRecentCollection['lieu']) ?></p>
        <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($mostRecentCollection['date_collecte'])) ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Prochaine Collecte</h3>
        <?php if ($nextCollection): ?>
            <p class="text-lg text-gray-600"><?= htmlspecialchars($nextCollection['lieu']) ?></p>
            <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($nextCollection['date_collecte'])) ?></p>
        <?php else: ?>
            <p class="text-lg text-gray-600">Aucune collecte à venir</p>
        <?php endif; ?>
    </div>
</section>

<!-- Collections Table -->
<div class="overflow-hidden rounded-lg shadow-lg bg-white">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-cyan-950 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Date</th>
                <th class="py-3 px-4 text-left">Lieu</th>
                <th class="py-3 px-4 text-left">Bénévoles</th>
                <th class="py-3 px-4 text-left">Types de déchets (quantité en kg)</th>
                <?php if ($_SESSION["role"] === "admin"): ?>
                    <th class="py-3 px-4 text-left">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-300">
            <?php foreach ($collectionsList as $collection): ?>
                <tr class="hover:bg-gray-100 transition duration-200">
                    <td class="py-3 px-4"><?= date($dateFormat, strtotime($collection['date_collecte'])) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($collection['lieu']) ?></td>
                    <td class="py-3 px-4"><?= $collection['benevoles'] ? htmlspecialchars($collection['benevoles']) : 'Aucun bénévole' ?></td>
                    <td class="py-3 px-4"><?= $collection['wasteDetails'] ?></td>
                    <?php if ($_SESSION["role"] === "admin"):
                        $editUrl = "collection_edit.php?id=" . $collection["id"];
                        $deleteUrl = "collection_delete.php?id=" . $collection["id"];
                        $editTitle = "Modifier la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
                        $deleteTitle = "Supprimer la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
                        require 'actionsButtons.php';
                    endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require 'paginationButtons.php'; ?>
<?php
$content = ob_get_clean();
require 'layout.php';
?>