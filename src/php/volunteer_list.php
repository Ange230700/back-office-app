<?php
require 'init.php';
require 'functions.php';

$pageTitle = "Liste des Bénévoles";
$pageHeader = "Liste des Bénévoles";

// SQL for volunteers (with participation details)
$volunteerSql = "SELECT
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

$countVolunteerSql = "SELECT COUNT(*) AS total FROM benevoles";

// Run the paginated query for volunteers
list($volunteersList, $totalPages) = runPaginatedQuery($pdo, $volunteerSql, $countVolunteerSql);

// Build table header HTML
$headerHtml = '
<tr>
    <th class="py-3 px-4 text-left">Nom</th>
    <th class="py-3 px-4 text-left">Email</th>
    <th class="py-3 px-4 text-left">Rôle</th>
    <th class="py-3 px-4 text-left">Participations</th>';
if ($_SESSION["role"] === "admin") {
    $headerHtml .= '<th class="py-3 px-4 text-left">Actions</th>';
}
$headerHtml .= '</tr>';

// Build table body HTML
$bodyHtml = '';
foreach ($volunteersList as $volunteer) {
    $bodyHtml .= '<tr class="hover:bg-gray-100 transition duration-200">';
    $bodyHtml .= '<td class="py-3 px-4">' . htmlspecialchars($volunteer["nom"]) . '</td>';
    $bodyHtml .= '<td class="py-3 px-4">' . htmlspecialchars($volunteer["email"]) . '</td>';
    $bodyHtml .= '<td class="py-3 px-4">' . htmlspecialchars($volunteer["role"]) . '</td>';
    $bodyHtml .= '<td class="py-3 px-4">' . htmlspecialchars($volunteer["participations"]) . '</td>';
    if ($_SESSION["role"] === "admin") {
        $editUrl = "volunteer_edit.php?id=" . $volunteer["id"];
        $deleteUrl = "volunteer_delete.php?id=" . $volunteer["id"];
        $editTitle = "Modifier " . htmlspecialchars($volunteer["nom"]);
        $deleteTitle = "Supprimer " . htmlspecialchars($volunteer["nom"]);
        ob_start();
        require 'actionsButtons.php';
        $actions = ob_get_clean();
        $bodyHtml .= '<td class="py-3 px-4">' . $actions . '</td>';
    }
    $bodyHtml .= '</tr>';
}

require 'table_template.php';
$paginationParams = getPaginationParams();
$pageNumber = $paginationParams['pageNumber'];
require 'paginationButtons.php';
$content = ob_get_clean();
require 'layout.php';
