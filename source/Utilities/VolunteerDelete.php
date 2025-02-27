<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;

class VolunteerDelete
{
    /**
     * Execute the deletion of a volunteer.
     */
    public static function run()
    {
        // Ensure that only admin users can delete a volunteer.
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        // Check if a valid volunteer ID is provided in the query string.
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int)$_GET['id'];
            $controller = new VolunteerController($pdo);
            $controller->eraseVolunteer($id);
            header("Location: /back-office-app/index.php?route=volunteer-list");
            exit();
        } else {
            echo "ID invalide.";
        }
    }
}
