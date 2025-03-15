<?php

// source\Views\Pages\CollectionAdd.php

namespace Kouak\BackOfficeApp\Views\Pages;

use PDOException;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\CollectionEvent\CollectionController;
use Kouak\BackOfficeApp\Controllers\CollectedWasteDetails\CollectedWasteDetailsController;
use Kouak\BackOfficeApp\Utilities\View;

class CollectionAdd
{
    public static function render()
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        $volunteerController = new VolunteerController($pdo);
        $volunteersList = $volunteerController->getVolunteersList();

        $collectedWasteController = new CollectedWasteDetailsController($pdo);
        $wasteTypesList = $collectedWasteController->getWasteTypesList();

        $collectionController = new CollectionController($pdo);
        $placesList = $collectionController->getCollectionPlacesList();

        $collection = [];
        $selectedVolunteersList = [];
        $collectedWastesList = [];

        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['csrf_token']) || !Session::verifyCsrfToken($_POST['csrf_token'])) {
                $error = "Le jeton CSRF est invalide. Veuillez réessayer.";
            } else {
                $submittedDate = $_POST["date"] ?? '';
                $submittedPlace = $_POST["collection_place"] ?? '';
                $volunteersAssigned = $_POST["Volunteer"] ?? [];
                $wasteTypesSubmitted = $_POST['waste_type'] ?? [];
                $quantitiesSubmitted = $_POST['quantity_kg'] ?? [];

                try {
                    $collectionController->addNewCollection(
                        $submittedDate,
                        $submittedPlace,
                        $volunteersAssigned,
                        $wasteTypesSubmitted,
                        $quantitiesSubmitted
                    );
                    header("Location: /back-office-app/collection-list");
                    exit;
                } catch (PDOException $e) {
                    $error = "Erreur de base de données : " . $e->getMessage();
                }
            }
        }

        $actionUrl = $_SERVER['PHP_SELF'] . "/collection-add";
        $cancelUrl = "/back-office-app/collection-list";
        $cancelTitle = "Retour à la liste des CollectionEvent";
        $buttonTitle = "Ajouter la collecte";
        $buttonTextContent = "Ajouter la collecte";

        $twig = View::getTwig();
        echo $twig->render('Pages/collection_add.twig', [
            'error'                 => $error,
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
            'error'                 => $error,
            'session'               => $_SESSION,
        ]);


        // Remove flash_error after the view has been rendered so it doesn't persist
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
    }
}
