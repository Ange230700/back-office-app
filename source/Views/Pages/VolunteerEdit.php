<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use PDOException;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\Collection\CollectionController;
use Kouak\BackOfficeApp\Views\Components\VolunteerForm;
use Kouak\BackOfficeApp\Views\Pages\Main;

class VolunteerEdit
{
    public static function render()
    {
        // Ensure only admin users can edit a volunteer
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        $destinationUrl = "Location: /back-office-app/index.php?route=volunteer-list";

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header($destinationUrl);
            exit;
        }
        $volunteerId = $_GET['id'];

        $volunteerController = new VolunteerController($pdo);
        $volunteer = $volunteerController->getEditableFieldsOfVolunteer($volunteerId);
        if (!$volunteer) {
            header($destinationUrl);
            exit;
        }

        // Assume we need the list of collections for participation assignments.
        $collectionController = new CollectionController($pdo);
        $collectionsList = $collectionController->getCollectionsList(); // Adjust accordingly
        $selectedCollections = $volunteerController->getCollectionsListVolunteerAttended($volunteerId);

        // Process POST submission
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

        $pageTitle = "Modifier un bénévole";
        $pageHeader = "Modifier un Bénévole";
        $actionUrl = $_SERVER['PHP_SELF'] . "?route=volunteer-edit&id=" . urlencode($volunteerId);
        $cancelUrl = "/back-office-app/index.php?route=volunteer-list";
        $cancelTitle = "Retour à la liste des bénévoles";
        $buttonTitle = "Modifier le bénévole";
        $buttonTextContent = "Modifier le bénévole";

        // Render the volunteer form using the VolunteerForm component.
        ob_start();
        VolunteerForm::render([
            'actionUrl'             => $actionUrl,
            'cancelUrl'             => $cancelUrl,
            'cancelTitle'           => $cancelTitle,
            'buttonTitle'           => $buttonTitle,
            'buttonTextContent'     => $buttonTextContent,
            'volunteer'             => $volunteer,
            'collectionsList'       => $collectionsList,
            'selectedCollections'   => $selectedCollections,
            'error'                 => $error,
        ]);
        $content = ob_get_clean();

        Main::render($pageTitle, $pageHeader, $content);
    }
}
