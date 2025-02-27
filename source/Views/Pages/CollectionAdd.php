<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\Collection\CollectionController;
use Kouak\BackOfficeApp\Controllers\CollectedWasteDetails\CollectedWasteDetailsController;
use Kouak\BackOfficeApp\Views\Components\CollectionForm;
use Kouak\BackOfficeApp\Views\Pages\Main;

class CollectionAdd
{
    public static function render()
    {
        // Ensure only admin users can add a collection
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        // Retrieve necessary data from respective controllers.
        $volunteerController = new VolunteerController($pdo);
        $volunteersList = $volunteerController->getVolunteersList(); // Assume this returns an array of volunteers

        $collectedWasteController = new CollectedWasteDetailsController($pdo);
        $wasteTypesList = $collectedWasteController->getWasteTypesList(); // Assume this returns an array of waste types

        $collectionController = new CollectionController($pdo);
        $placesList = $collectionController->getPlacesList(); // Assume this method exists in your OOP controller

        // Initialize empty/default data for add mode.
        $collection = []; // No pre-existing collection data.
        $selectedVolunteersList = [];
        $collectedWastesList = []; // No existing waste entries.

        // Process POST submission
        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $submittedDate = $_POST["date"] ?? '';
            $submittedPlace = $_POST["lieu"] ?? '';
            $volunteersAssigned = $_POST["benevoles"] ?? [];
            $wasteTypesSubmitted = $_POST['type_dechet'] ?? [];
            $quantitiesSubmitted = $_POST['quantite_kg'] ?? [];

            try {
                $collectionController->addNewCollection(
                    $submittedDate,
                    $submittedPlace,
                    $volunteersAssigned,
                    $wasteTypesSubmitted,
                    $quantitiesSubmitted
                );
                header("Location: /back-office-app/index.php?route=collection-list");
                exit;
            } catch (\PDOException $e) {
                $error = "Erreur de base de données : " . $e->getMessage();
            }
        }

        // Set page variables
        $pageTitle = "Ajouter une collecte";
        $pageHeader = "Ajouter une collecte";
        $actionUrl = $_SERVER['PHP_SELF'] . "?route=collection-add"; // route-based URL
        $cancelUrl = "/back-office-app/index.php?route=collection-list";
        $cancelTitle = "Retour à la liste des collectes";
        $buttonTitle = "Ajouter la collecte";
        $buttonTextContent = "Ajouter la collecte";

        // Render the collection form using our OOP component.
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
            'collectedWastesList'   => $collectedWastesList,
            'error'                 => $error
        ]);
        $content = ob_get_clean();

        // Render the page using the Main layout.
        Main::render($pageTitle, $pageHeader, $content);
    }
}
