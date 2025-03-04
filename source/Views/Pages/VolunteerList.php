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
        Helpers::checkUserLoggedIn();
        $pdo = Configuration::getPdo();

        $volunteerController = new VolunteerController($pdo);
        list($volunteersList, $numberOfPages) = $volunteerController->getVolunteersFullDetailsPaginated();

        $role = Session::getSession("role");

        $pageNumber = $_GET['pageNumber'] ?? 1;

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
