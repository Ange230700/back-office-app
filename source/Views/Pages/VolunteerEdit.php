<?php

// source\Views\Pages\VolunteerEdit.php

namespace Kouak\BackOfficeApp\Views\Pages;

use PDOException;
use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\CollectionEvent\CollectionController;
use Kouak\BackOfficeApp\Utilities\View;

class VolunteerEdit
{
    public static function render($volunteer_id)
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        $destinationUrl = "Location: /back-office-app/volunteer-list";

        if (empty($volunteer_id)) {
            header($destinationUrl);
            exit;
        }
        $volunteerId = $volunteer_id;

        $volunteerController = new VolunteerController($pdo);
        $volunteer = $volunteerController->getEditableFieldsOfVolunteer($volunteerId);
        if (!$volunteer) {
            header($destinationUrl);
            exit;
        }

        $collectionController = new CollectionController($pdo);
        $collectionsList = $collectionController->getCollectionsList();
        $selectedCollections = $volunteerController->getCollectionsListVolunteerAttended($volunteerId);

        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['csrf_token']) || !Session::verifyCsrfToken($_POST['csrf_token'])) {
                $error = "Le jeton CSRF est invalide. Veuillez réessayer.";
            } else {
                $submittedRole = $_POST["role"] ?? $volunteer['role'];
                $submittedParticipations = $_POST['attendances'] ?? [];
                try {
                    $volunteerController->editVolunteer($submittedRole, $volunteerId, $submittedParticipations);
                    header($destinationUrl);
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur de base de données : " . $e->getMessage();
                }
            }
        }

        $actionUrl = $_SERVER['PHP_SELF'] . "/volunteer-edit/" . urlencode($volunteerId);
        $cancelUrl = "/back-office-app/volunteer-list";
        $cancelTitle = "Retour à la liste des bénévoles";
        $buttonTitle = "Modifier le bénévole";
        $buttonTextContent = "Modifier le bénévole";

        $twig = View::getTwig();
        echo $twig->render('Pages/volunteer_edit.twig', [
            'error'               => $error,
            'actionUrl'           => $actionUrl,
            'cancelUrl'           => $cancelUrl,
            'cancelTitle'         => $cancelTitle,
            'buttonTitle'         => $buttonTitle,
            'buttonTextContent'   => $buttonTextContent,
            'volunteer'           => $volunteer,
            'collectionsList'     => $collectionsList,
            'selectedCollections' => $selectedCollections,
            'session'             => $_SESSION,
        ]);

        // Remove flash_error after the view has been rendered so it doesn't persist
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
    }
}
