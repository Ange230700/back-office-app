<?php

// source\Utilities\VolunteerDelete.php

namespace Kouak\BackOfficeApp\Utilities;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class VolunteerDelete
{
    public static function runVolunteerDeletion($volunteer_id)
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();
        $destinationUrl = UrlGenerator::generate('/volunteer-list');
        if (empty($volunteer_id)) {
            return new RedirectResponse($destinationUrl);
        }
        $volunteerId = $volunteer_id;
        $controller = new VolunteerController($pdo);
        $volunteerDetails = $controller->getEditableFieldsOfVolunteer($volunteerId);
        if ($volunteerDetails) {
            if (in_array($volunteerDetails['email'], ['admin@admin.admin', 'user@user.user']) || ($volunteerDetails['role'] === 'superAdmin')) {
                Session::setSession("flash_error", "Vous ne pouvez pas supprimer ce compte.");
                return new RedirectResponse($destinationUrl);
            }
        } else {
            return new RedirectResponse($destinationUrl);
        }
        $controller->eraseVolunteer($volunteerId);
        Session::setSession("flash_success", "Le bénévole a été supprimé avec succès.");
        return new RedirectResponse($destinationUrl);
    }
}
