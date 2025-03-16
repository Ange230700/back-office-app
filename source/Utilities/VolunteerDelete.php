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
            header("Location: /back-office-app/public/volunteer-list");
            exit;
        }

        $volunteerId = $volunteer_id;
        $controller = new VolunteerController($pdo);

        // Retrieve volunteer details to check if the account is protected
        $volunteerDetails = $controller->getEditableFieldsOfVolunteer($volunteerId);
        if ($volunteerDetails) {
            // Prevent deletion of demo accounts and superAdmin accounts
            if (in_array($volunteerDetails['email'], ['admin@admin.admin', 'user@user.user']) || ($volunteerDetails['role'] === 'superAdmin')) {
                Session::setSession("flash_error", "Vous ne pouvez pas supprimer ce compte.");
                header("Location: /back-office-app/public/volunteer-list");
                exit;
            }
        } else {
            // If the volunteer details cannot be found, just redirect
            header("Location: /back-office-app/public/volunteer-list");
            exit;
        }

        $controller->eraseVolunteer($volunteerId);
        Session::setSession("flash_success", "Le bénévole a été supprimé avec succès.");
        header("Location: /back-office-app/public/volunteer-list");
        exit();
    }
}
