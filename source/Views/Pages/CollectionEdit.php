<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\Collection\CollectionController;
use Kouak\BackOfficeApp\Controllers\CollectedWasteDetails\CollectedWasteDetailsController;
use Kouak\BackOfficeApp\Utilities\View;

class CollectionEdit
{
    public static function render()
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();

        $destinationUrl = "Location: /back-office-app/index.php?route=collection-list";

        if (!isset($_GET['id']) || empty($_GET['id'])) {
            header($destinationUrl);
            exit;
        }
        $collectionId = $_GET['id'];

        $collectionController = new CollectionController($pdo);
        $collection = $collectionController->getCollection($collectionId);
        if (!$collection) {
            header($destinationUrl);
            exit;
        }

        $volunteerController = new VolunteerController($pdo);
        $selectedVolunteersList = $collectionController->getVolunteersListWhoAttendedCollection($collectionId);
        $volunteersList = $volunteerController->getVolunteersList();
        $collectedWasteController = new CollectedWasteDetailsController($pdo);
        $wasteTypesList = $collectedWasteController->getWasteTypesList();
        $placesList = $collectionController->getCollectionPlacesList();
        $collectedWasteDetailsList = $collectedWasteController->getCollectedWasteDetailsList($collectionId);
        if (empty($collectedWasteDetailsList)) {
            $collectedWasteDetailsList[] = ['type_dechet' => '', 'quantite_kg' => ''];
        }

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

        $actionUrl = $_SERVER['PHP_SELF'] . "?route=collection-edit&id=" . urlencode($collectionId);
        $cancelUrl = "/back-office-app/index.php?route=collection-list";
        $cancelTitle = "Retour à la liste des collectes";
        $buttonTitle = "Modifier la collecte";
        $buttonTextContent = "Modifier la collecte";

        $twig = View::getTwig();
        echo $twig->render('Pages/collection_edit.twig', [
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
            'collectedWastesList'   => $collectedWasteDetailsList,
            'error'                 => $error,
            'session'               => $_SESSION,
        ]);
    }
}
