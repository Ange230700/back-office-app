<?php

// source\Utilities\VolunteerDelete.php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;

class VolunteerDelete
{
    public static function runVolunteerDeletion($volunteer_id)
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        if (empty($volunteer_id)) {
            header("Location: /back-office-app/volunteer-list");
            exit;
        }

        $volunteerId = $volunteer_id;
        $controller = new VolunteerController($pdo);
        $controller->eraseVolunteer($volunteerId);
        header("Location: /back-office-app/volunteer-list");
        exit();
    }
}
