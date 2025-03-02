<?php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;

class VolunteerDelete
{
    public static function run()
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

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
