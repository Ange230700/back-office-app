<?php
require 'init.php';

$sqlQueryToSelectVolunteersFullDetails = "SELECT
    benevoles.id,
    benevoles.nom,
    benevoles.email,
    benevoles.role,
    COALESCE(
        GROUP_CONCAT(CONCAT(collectes.lieu, ' (', collectes.date_collecte, ')') SEPARATOR ', '),
        'Aucune participation pour le moment'
    ) AS participations
    FROM benevoles
    LEFT JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
    LEFT JOIN collectes ON collectes.id = benevoles_collectes.id_collecte
    GROUP BY benevoles.id
    ORDER BY benevoles.nom ASC
    LIMIT :limit OFFSET :offset";

$sqlQueryToCountNumberOfVolunteers = "SELECT COUNT(*) AS total FROM benevoles";
list($volunteersList, $totalPages) = runPaginatedQuery($pdo, $sqlQueryToSelectVolunteersFullDetails, $sqlQueryToCountNumberOfVolunteers);

$pageTitle = "Liste des Bénévoles";
$pageHeader = "Liste des Bénévoles";

$tableHeadersRow = '
    <tr>
        <th class="py-3 px-4 text-left">Nom</th>
        <th class="py-3 px-4 text-left">Email</th>
        <th class="py-3 px-4 text-left">Rôle</th>
        <th class="py-3 px-4 text-left">Participations</th>';
if ($_SESSION["role"] === "admin") {
    $tableHeadersRow .= '<th class="py-3 px-4 text-left">Actions</th>';
}
$tableHeadersRow .= '</tr>';

$tableBody = '';
foreach ($volunteersList as $volunteer) {
    $tableBody .= '<tr class="hover:bg-gray-100 transition duration-200">';
    $tableBody .= '<td class="py-3 px-4">' . htmlspecialchars($volunteer["nom"]) . '</td>';
    $tableBody .= '<td class="py-3 px-4">' . htmlspecialchars($volunteer["email"]) . '</td>';
    $tableBody .= '<td class="py-3 px-4">' . htmlspecialchars($volunteer["role"]) . '</td>';
    $tableBody .= '<td class="py-3 px-4">' . htmlspecialchars($volunteer["participations"]) . '</td>';
    if ($_SESSION["role"] === "admin") {
        $editUrl = "volunteer_edit.php?id=" . $volunteer["id"];
        $deleteUrl = "volunteer_delete.php?id=" . $volunteer["id"];
        $editTitle = "Modifier " . htmlspecialchars($volunteer["nom"]);
        $deleteTitle = "Supprimer " . htmlspecialchars($volunteer["nom"]);
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
