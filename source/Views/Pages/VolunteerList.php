<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Views\Components\ActionButtons;
use Kouak\BackOfficeApp\Views\Components\TableTemplate;
use Kouak\BackOfficeApp\Views\Components\PaginationButtons;
use Kouak\BackOfficeApp\Views\Pages\Main;

class VolunteerList
{
    public static function render()
    {
        // Ensure the user is logged in
        Helpers::checkUserLoggedIn();
        $pdo = Configuration::getPdo();

        // Use the VolunteerController to get needed data.
        $volunteerController = new VolunteerController($pdo);
        list($volunteersList, $numberOfPages) = $volunteerController->getVolunteersFullDetailsPaginated();

        $pageTitle = "Liste des Bénévoles";
        $pageHeader = "Liste des Bénévoles";

        // Build table headers
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
                $editUrl = "/back-office-app/index.php?route=volunteer-edit&id=" . $volunteer["id"];
                $deleteUrl = "/back-office-app/index.php?route=volunteer-delete&id=" . $volunteer["id"];
                $editTitle = "Modifier " . htmlspecialchars($volunteer["nom"]);
                $deleteTitle = "Supprimer " . htmlspecialchars($volunteer["nom"]);
                ob_start();
                ActionButtons::render($editUrl, $deleteUrl, $editTitle, $deleteTitle);
                $actions = ob_get_clean();
                $tableBody .= '<td class="py-3 px-4">' . $actions . '</td>';
            }
            $tableBody .= '</tr>';
        }

        // Capture table and pagination output
        ob_start();
        TableTemplate::render($tableHeadersRow, $tableBody);
        $tableHtml = ob_get_clean();

        ob_start();
        PaginationButtons::render($numberOfPages, null, 'volunteer-list');
        $paginationHtml = ob_get_clean();

        // Compose content
        ob_start();
        echo $tableHtml;
        echo $paginationHtml;
        $content = ob_get_clean();

        // Render using the Main layout
        Main::render($pageTitle, $pageHeader, $content);
    }
}
