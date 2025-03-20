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
        $baseUrl = Helpers::getBaseUrl();
        $destinationUrl = "Location: " . $baseUrl . "/volunteer-list";
        if (empty($volunteer_id)) {
            header($destinationUrl);
            exit;
        }
        $volunteerId = $volunteer_id;
        $controller = new VolunteerController($pdo);
        $volunteerDetails = $controller->getEditableFieldsOfVolunteer($volunteerId);
        if ($volunteerDetails) {
            if (in_array($volunteerDetails['email'], ['admin@admin.admin', 'user@user.user']) || ($volunteerDetails['role'] === 'superAdmin')) {
                Session::setSession("flash_error", "Vous ne pouvez pas supprimer ce compte.");
                header($destinationUrl);
                exit;
            }
        } else {
            header($destinationUrl);
            exit;
        }
        $controller->eraseVolunteer($volunteerId);
        Session::setSession("flash_success", "Le bénévole a été supprimé avec succès.");
        header($destinationUrl);
        exit();
    }
}
