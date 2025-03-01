<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\Collection\CollectionController;
use Kouak\BackOfficeApp\Controllers\CollectedWasteDetails\CollectedWasteDetailsController;
use Kouak\BackOfficeApp\Views\Components\CollectionForm;
use Kouak\BackOfficeApp\Views\Pages\Main;

class CollectionEdit
{
    public static function render()
    {
        // Ensure admin privileges
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        $destinationUrl = "Location: /back-office-app/index.php?route=collection-list";

        // Get collection ID from GET parameters
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header($destinationUrl);
            exit;
        }
        $collectionId = $_GET['id'];

        // Create a CollectionController instance
        $collectionController = new CollectionController($pdo);
        // Retrieve the collection
        $collection = $collectionController->getCollection($collectionId);
        if (!$collection) {
            header($destinationUrl);
            exit;
        }

        // Retrieve pre-selected volunteers and waste details
        $volunteerController = new VolunteerController($pdo);

        // Get lists from controllers
        $selectedVolunteersList = $collectionController->getVolunteersListWhoAttendedCollection($collectionId);
        $volunteersList = $volunteerController->getVolunteersList();
        $collectedWasteController = new CollectedWasteDetailsController($pdo);
        $wasteTypesList = $collectedWasteController->getWasteTypesList();
        $placesList = $collectionController->getPlacesList();
        $collectedWasteDetailsList = $collectedWasteController->getCollectedWasteDetailsList($collectionId);
        if (empty($collectedWasteDetailsList)) {
            $collectedWasteDetailsList[] = ['type_dechet' => '', 'quantite_kg' => ''];
        }

        // Process POST submission
        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['csrf_token']) || !Session::verifyCsrfToken($_POST['csrf_token'])) {
                $error = "Le jeton CSRF est invalide. Veuillez réessayer.";
            } else {
                $submittedDate = $_POST["date"] ?? '';
                $submittedPlace = $_POST["lieu"] ?? '';
                $volunteersAssigned = $_POST["benevoles"] ?? [];
                $wasteTypesSubmitted = $_POST['type_dechet'] ?? [];
                $quantitiesSubmitted = $_POST['quantite_kg'] ?? [];

                try {
                    $collectionController->editCollection(
                        $submittedDate,
                        $submittedPlace,
                        $collectionId,
                        $volunteersAssigned,
                        $wasteTypesSubmitted,
                        $quantitiesSubmitted
                    );
                    header($destinationUrl);
                    exit;
                } catch (\PDOException $e) {
                    $error = "Erreur de base de données : " . $e->getMessage();
                }
            }
        }

        // Set page variables
        $pageTitle = "Modifier une collecte";
        $pageHeader = "Modifier une collecte";
        $actionUrl = $_SERVER['PHP_SELF'] . "?route=collection-edit&id=" . urlencode($collectionId);
        $cancelUrl = "/back-office-app/index.php?route=collection-list";
        $cancelTitle = "Retour à la liste des collectes";
        $buttonTitle = "Modifier la collecte";
        $buttonTextContent = "Modifier la collecte";

        // Render the collection form using the CollectionForm component
        ob_start();
        CollectionForm::render([
            'actionUrl'             => $actionUrl,
            'cancelUrl'             => $cancelUrl,
            'cancelTitle'           => $cancelTitle,
            'buttonTitle'           => $buttonTitle,
            'buttonTextContent'     => $buttonTextContent,
            'volunteersList'        => $volunteersList,
            'wasteTypesList'        => $wasteTypesList,
            'placesList'            => $placesList,
            'collection'            => $collection,
            'selectedVolunteersList' => $selectedVolunteersList,
            'collectedWastesList'   => $collectedWasteDetailsList,
            'error'                 => $error
        ]);
        $content = ob_get_clean();

        // Render the page using the Main layout
        Main::render($pageTitle, $pageHeader, $content);
    }
}
