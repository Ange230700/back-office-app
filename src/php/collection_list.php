<?php
require 'init.php';
require 'functions.php';

// Define date format
$dateFormat = "d/m/Y";

// Main SQL for collections (including join to display volunteers and waste details)
$collectionSql = "SELECT
    benevoles_collectes.id_collecte AS id,
    collectes.date_collecte,
    collectes.lieu,
    GROUP_CONCAT(DISTINCT benevoles.nom ORDER BY benevoles.nom SEPARATOR ', ') AS benevoles,
    GROUP_CONCAT(DISTINCT CONCAT(COALESCE(dechets_collectes.type_dechet, 'type(s) non défini(s)'), ' (', ROUND(COALESCE(dechets_collectes.quantite_kg, 0), 1), 'kg)')
        ORDER BY dechets_collectes.type_dechet SEPARATOR ', ') AS wasteDetails
FROM benevoles
INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte
LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte
GROUP BY benevoles_collectes.id_collecte
ORDER BY collectes.date_collecte DESC
LIMIT :limit OFFSET :offset";

$countCollectionSql = "SELECT COUNT(DISTINCT benevoles_collectes.id_collecte) AS total
                       FROM benevoles
                       INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
                       INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte";

// Run the paginated query
list($collectionsList, $totalPages) = runPaginatedQuery($pdo, $collectionSql, $countCollectionSql);

// Additional queries specific to collections (statistics)
$totalWasteSql = "SELECT COALESCE(ROUND(SUM(COALESCE(dechets_collectes.quantite_kg,0)),1), 0) AS quantite_total_des_dechets_collectes
                  FROM collectes
                  LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte";
$totalWasteStmt = $pdo->prepare($totalWasteSql);
if (!$totalWasteStmt->execute()) {
    die("Erreur lors de l'exécution de la requête SQL.");
}
$totalWaste = $totalWasteStmt->fetch(PDO::FETCH_ASSOC)['quantite_total_des_dechets_collectes'];

$recentCollectionSql = "SELECT lieu, date_collecte FROM collectes
                        WHERE date_collecte <= CURDATE()
                        ORDER BY date_collecte DESC
                        LIMIT 1";
$recentStmt = $pdo->prepare($recentCollectionSql);
if (!$recentStmt->execute()) {
    die("Erreur lors de l'exécution de la requête SQL.");
}
$mostRecentCollection = $recentStmt->fetch();

$nextCollectionSql = "SELECT lieu, date_collecte FROM collectes
                      WHERE date_collecte > CURDATE()
                      ORDER BY date_collecte ASC
                      LIMIT 1";
$nextStmt = $pdo->prepare($nextCollectionSql);
if (!$nextStmt->execute()) {
    die("Erreur lors de l'exécution de la requête SQL.");
}
$nextCollection = $nextStmt->fetch();

$pageTitle = "Liste des Collectes";
$pageHeader = "Liste des Collectes de Déchets";

// Build table header HTML
$headerHtml = '
<tr>
    <th class="py-3 px-4 text-left">Date</th>
    <th class="py-3 px-4 text-left">Lieu</th>
    <th class="py-3 px-4 text-left">Bénévoles</th>
    <th class="py-3 px-4 text-left">Types de déchets (quantité en kg)</th>';
if ($_SESSION["role"] === "admin") {
    $headerHtml .= '<th class="py-3 px-4 text-left">Actions</th>';
}
$headerHtml .= '</tr>';

// Build table body HTML
$bodyHtml = '';
foreach ($collectionsList as $collection) {
    $bodyHtml .= '<tr class="hover:bg-gray-100 transition duration-200">';
    $bodyHtml .= '<td class="py-3 px-4">' . date($dateFormat, strtotime($collection['date_collecte'])) . '</td>';
    $bodyHtml .= '<td class="py-3 px-4">' . htmlspecialchars($collection['lieu']) . '</td>';
    $bodyHtml .= '<td class="py-3 px-4">' . ($collection['benevoles'] ? htmlspecialchars($collection['benevoles']) : 'Aucun bénévole') . '</td>';
    $bodyHtml .= '<td class="py-3 px-4">' . $collection['wasteDetails'] . '</td>';
    if ($_SESSION["role"] === "admin") {
        $editUrl = "collection_edit.php?id=" . $collection["id"];
        $deleteUrl = "collection_delete.php?id=" . $collection["id"];
        $editTitle = "Modifier la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
        $deleteTitle = "Supprimer la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
        ob_start();
        require 'actionsButtons.php';
        $actions = ob_get_clean();
        $bodyHtml .= '<td class="py-3 px-4">' . $actions . '</td>';
    }
    $bodyHtml .= '</tr>';
}
?>
<!-- Statistics Section -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des Collectes</h3>
        <p class="text-3xl font-bold text-blue-600"><?= $totalPages * getPaginationParams()['limit'] /* or use total from count query */ ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des déchets collectés</h3>
        <p class="text-3xl font-bold text-blue-600"><?= $totalWaste ?> kg</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Dernière Collecte</h3>
        <?php if ($mostRecentCollection): ?>
            <p class="text-lg text-gray-600"><?= htmlspecialchars($mostRecentCollection['lieu']) ?></p>
            <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($mostRecentCollection['date_collecte'])) ?></p>
        <?php else: ?>
            <p class="text-lg text-gray-600">Aucune collecte pour le moment</p>
        <?php endif; ?>
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
<?php require 'table_template.php'; ?>

<?php $paginationParams = getPaginationParams();
$pageNumber = $paginationParams['pageNumber'];
require 'paginationButtons.php'; ?>
<?php
$content = ob_get_clean();
require 'layout.php';
