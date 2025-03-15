<?php

// source\Views\Pages\VolunteerList.php

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

        $role = Session::getSession("role");
        $volunteerController = new VolunteerController($pdo);
        list($volunteersList, $numberOfPages) = $volunteerController->getVolunteersFullDetailsPaginated($role);

        // Remove emails if the user is not superAdmin
        if ($role !== 'superAdmin') {
            foreach ($volunteersList as &$volunteer) {
                unset($volunteer['email']);
            }
        }

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

        // Remove flash_error after the view has been rendered so it doesn't persist
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
    }
}
