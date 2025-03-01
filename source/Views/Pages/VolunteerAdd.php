<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\Collection\CollectionController;
use Kouak\BackOfficeApp\Views\Components\VolunteerForm;
use Kouak\BackOfficeApp\Views\Pages\Main;

class VolunteerAdd
{
    public static function render()
    {
        // Ensure only admin users can add a volunteer
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        $volunteerController = new VolunteerController($pdo);
        // For adding, we might also need a list of collections to assign participations.
        // Assume we have a method getCollectionsList() (or similar) in a CollectionController.
        // For brevity, let's assume we have a simplified collections list:
        $collectionController = new CollectionController($pdo);
        $collectionsList = $collectionController->getCollectionsList(); // Adjust accordingly

        // Process POST submission
        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['csrf_token']) || !Session::verifyCsrfToken($_POST['csrf_token'])) {
                $error = "Le jeton CSRF est invalide. Veuillez réessayer.";
            } else {
                $submittedName = $_POST['nom'] ?? '';
                $submittedEmail = $_POST['email'] ?? '';
                $submittedPassword = $_POST['mot_de_passe'] ?? '';
                $hashedPassword = password_hash($submittedPassword, PASSWORD_DEFAULT);
                $submittedRole = $_POST['role'] ?? 'participant';
                $submittedParticipations = $_POST['attendances'] ?? [];

                try {
                    $volunteerController->addVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole, $submittedParticipations);
                    header("Location: /back-office-app/index.php?route=volunteer-list");
                    exit;
                } catch (\PDOException $e) {
                    $error = "Erreur de base de données : " . $e->getMessage();
                }
            }
        }

        // Set page variables
        $pageTitle = "Ajouter un bénévole";
        $pageHeader = "Ajouter un Bénévole";
        $actionUrl = $_SERVER['PHP_SELF'] . "?route=volunteer-add";
        $cancelUrl = "/back-office-app/index.php?route=volunteer-list";
        $cancelTitle = "Retour à la liste des bénévoles";
        $buttonTitle = "Ajouter le bénévole";
        $buttonTextContent = "Ajouter le bénévole";

        // For add mode, volunteer data is empty.
        $volunteer = [];
        $selectedCollections = [];

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

        // Render the page using the Main layout.
        Main::render($pageTitle, $pageHeader, $content);
    }
}
