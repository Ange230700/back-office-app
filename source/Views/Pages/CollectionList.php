<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Collection\CollectionController;
use Kouak\BackOfficeApp\Views\Components\ActionButtons;
use Kouak\BackOfficeApp\Views\Components\TableTemplate;
use Kouak\BackOfficeApp\Views\Components\PaginationButtons;

class CollectionList
{
    public static function render()
    {
        // Ensure the user is logged in
        Helpers::checkUserLoggedIn();
        $pdo = Configuration::getPdo();

        // Use the CollectionController (OOP) to get needed data
        $controller = new CollectionController($pdo);
        $collectedWastesTotalQuantity = $controller->getCollectedWastesTotalQuantity();
        $mostRecentCollection = $controller->getMostRecentCollection();
        $nextCollection = $controller->getNextCollection();

        list($collectionsList, $totalPages) = $controller->getCollectionsListPaginated();
        $dateFormat = "d/m/Y";

        // Build table headers and body
        $tableHeadersRow = '
            <tr>
                <th class="py-3 px-4 text-left">Date</th>
                <th class="py-3 px-4 text-left">Lieu</th>
                <th class="py-3 px-4 text-left">Bénévoles</th>
                <th class="py-3 px-4 text-left">Types de déchets (quantité en kg)</th>';
        $role = Session::get("role");
        if ($role === "admin") {
            $tableHeadersRow .= '<th class="py-3 px-4 text-left">Actions</th>';
        }
        $tableHeadersRow .= '</tr>';

        $tableBody = '';
        foreach ($collectionsList as $collection) {
            $tableBody .= '<tr class="hover:bg-gray-100 transition duration-200">';
            $tableBody .= '<td class="py-3 px-4">' . date($dateFormat, strtotime($collection['date_collecte'])) . '</td>';
            $tableBody .= '<td class="py-3 px-4">' . htmlspecialchars($collection['lieu']) . '</td>';
            $tableBody .= '<td class="py-3 px-4">' . ($collection['benevoles'] ? htmlspecialchars($collection['benevoles']) : 'Aucun bénévole') . '</td>';
            $tableBody .= '<td class="py-3 px-4">' . $collection['wasteDetails'] . '</td>';
            if ($role === "admin") {
                $editUrl = "/back-office-app/index.php?route=collection-edit&id=" . $collection["id"];
                $deleteUrl = "/back-office-app/index.php?route=collection-delete&id=" . $collection["id"];
                $editTitle = "Modifier la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
                $deleteTitle = "Supprimer la collecte du " . date($dateFormat, strtotime($collection['date_collecte'])) . " à " . htmlspecialchars($collection['lieu']);
                // Render action buttons using a namespaced component
                ob_start();
                ActionButtons::render($editUrl, $deleteUrl, $editTitle, $deleteTitle);
                $actions = ob_get_clean();
                $tableBody .= '<td class="py-3 px-4">' . $actions . '</td>';
            }
            $tableBody .= '</tr>';
        }

        // Capture the table template output
        ob_start();
        TableTemplate::render($tableHeadersRow, $tableBody);
        $tableHtml = ob_get_clean();

        // Capture pagination buttons output
        ob_start();
        PaginationButtons::render($totalPages, null, 'collection-list');
        $paginationHtml = ob_get_clean();

        // Compose the content for the Main layout
        ob_start();
        echo $tableHtml;
        echo $paginationHtml;
        $content = ob_get_clean();

        // Prepare dashboard data for Main layout's dashboard section
        $dashboardData = [
            'totalPages' => $totalPages,
            'collectedWastesTotalQuantity' => $collectedWastesTotalQuantity,
            'mostRecentCollection' => $mostRecentCollection,
            'nextCollection' => $nextCollection,
            'dateFormat' => $dateFormat,
        ];

        // Render using the Main layout
        Main::render("Liste des Collectes", "Liste des Collectes de Déchets", $content, $dashboardData);
    }
}
