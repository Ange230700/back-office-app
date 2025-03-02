<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Utilities\View;

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
        $role = Session::get("role");

        // Get current page number from query params.
        $pageNumber = $_GET['pageNumber'] ?? 1;

        // Render the template using Twig.
        $twig = View::getTwig();
        echo $twig->render('Pages/volunteer_list.twig', [
            'volunteers'  => $volunteersList,
            'totalPages'  => $numberOfPages,
            'role'        => $role,
            'pageNumber'  => $pageNumber,
            'route'       => 'volunteer-list',
            'session'     => $_SESSION,
        ]);
    }
}
