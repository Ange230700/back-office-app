<?php
require 'init.php';

$collectedWastesTotalQuantity = getCollectedWastesTotalQuantity($pdo);
$mostRecentCollection = getMostRecentCollection($pdo);
$nextCollection = getNextCollection($pdo);

$sqlQueryToSelectCollectionsFullDetails = "SELECT
    benevoles_collectes.id_collecte AS id,
    collectes.date_collecte,
    collectes.lieu,
    GROUP_CONCAT(DISTINCT benevoles.nom ORDER BY benevoles.nom SEPARATOR ', ')  AS benevoles,
    GROUP_CONCAT(DISTINCT CONCAT(COALESCE(dechets_collectes.type_dechet, 'type  (s) non défini(s)'), ' (', ROUND(COALESCE(dechets_collectes.quantite_kg, 0),  1), 'kg)')
    ORDER BY dechets_collectes.type_dechet SEPARATOR ', ') AS wasteDetails
    FROM benevoles
    INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.   id_benevole
    INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte
    LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte
    GROUP BY benevoles_collectes.id_collecte
    ORDER BY collectes.date_collecte DESC
    LIMIT :limit OFFSET :offset";

$sqlQueryToCountNumberOfCollections = "SELECT
    COUNT(DISTINCT benevoles_collectes.id_collecte)
    AS total
    FROM benevoles
    INNER JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
    INNER JOIN collectes ON collectes.id = benevoles_collectes.id_collecte";

list($collectionsList, $totalPages) = runPaginatedQuery($pdo, $sqlQueryToSelectCollectionsFullDetails, $sqlQueryToCountNumberOfCollections);

$pageTitle = "Liste des Collectes";
$pageHeader = "Liste des Collectes de Déchets";

$tableHeadersRow = '
    <tr>
        <th class="py-3 px-4 text-left">Date</th>
        <th class="py-3 px-4 text-left">Lieu</th>
        <th class="py-3 px-4 text-left">Bénévoles</th>
        <th class="py-3 px-4 text-left">Types de déchets (quantité en kg)</th>';
if ($_SESSION["role"] === "admin") {
    $tableHeadersRow .= '<th class="py-3 px-4 text-left">Actions</th>';
}
$tableHeadersRow .= '</tr>';

$dateFormat = "d/m/Y";
$tableBody = '';
foreach ($collectionsList as $collection) {
    $tableBody .= '<tr class="hover:bg-gray-100 transition duration-200">';
    $tableBody .= '<td class="py-3 px-4">' . date($dateFormat, strtotime($collection['date_collecte'])) . '</td>';
    $tableBody .= '<td class="py-3 px-4">' . htmlspecialchars($collection['lieu']) . '</td>';
    $tableBody .= '<td class="py-3 px-4">' . ($collection['benevoles'] ? htmlspecialchars($collection['benevoles']) : 'Aucun bénévole') . '</td>';
    $tableBody .= '<td class="py-3 px-4">' . $collection['wasteDetails'] . '</td>';
    if ($_SESSION["role"] === "admin") {
        $editUrl = "collection_edit.php?id=" . $collection["id"];
        $deleteUrl = "collection_delete.php?id=" . $collection["id"];
        $editTitle = "Modifier la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
        $deleteTitle = "Supprimer la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
        ob_start();
        require 'actionsButtonsComponent.php';
        $actions = ob_get_clean();
        $tableBody .= '<td class="py-3 px-4">' . $actions . '</td>';
    }
    $tableBody .= '</tr>';
}
require 'tableTemplateComponent.php';

$paginationParams = getPaginationParams();
$pageNumber = $paginationParams['pageNumber'];
require 'paginationButtonsComponent.php';

$content = ob_get_clean();
require 'layoutPage.php';
