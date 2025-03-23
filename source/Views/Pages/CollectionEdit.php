<?php

// source\Views\Pages\CollectionEdit.php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\CollectionEvent\CollectionController;
use Kouak\BackOfficeApp\Controllers\CollectedWasteDetails\CollectedWasteDetailsController;
use Kouak\BackOfficeApp\Utilities\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Kouak\BackOfficeApp\Utilities\UrlGenerator;

class CollectionEdit
{
    public static function render($collection_id)
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();
        $destinationUrl = UrlGenerator::generate('/collection-list');
        if (empty($collection_id)) {
            return new RedirectResponse($destinationUrl);
        }
        $collectionId = $collection_id;
        $collectionController = new CollectionController($pdo);
        $collection = $collectionController->getCollection($collectionId);
        if (!$collection) {
            return new RedirectResponse($destinationUrl);
        }
        $volunteerController = new VolunteerController($pdo);
        $selectedVolunteersList = $collectionController->getVolunteersListWhoAttendedCollection($collectionId);
        $volunteersList = $volunteerController->getVolunteersList();
        $collectedWasteController = new CollectedWasteDetailsController($pdo);
        $wasteTypesList = $collectedWasteController->getWasteTypesList();
        $placesList = $collectionController->getCollectionPlacesList();
        $collectedWasteDetailsList = $collectedWasteController->getCollectedWasteDetailsList($collectionId);
        if (empty($collectedWasteDetailsList)) {
            $collectedWasteDetailsList[] = ['waste_type' => '', 'quantity_kg' => ''];
        }
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
                    $collectionController->editCollection(
                        $submittedDate,
                        $submittedPlace,
                        $collectionId,
                        $volunteersAssigned,
                        $wasteTypesSubmitted,
                        $quantitiesSubmitted
                    );
                    return new RedirectResponse($destinationUrl);
                } catch (\PDOException $e) {
                    $error = "Erreur de base de données : " . $e->getMessage();
                }
            }
        }
        $actionUrl = UrlGenerator::generate("/collection-edit/" . urlencode($collectionId));
        $cancelUrl = UrlGenerator::generate('/collection-list');
        $cancelTitle = "Retour à la liste des CollectionEvent";
        $buttonTitle = "Modifier la collecte";
        $buttonTextContent = "Modifier la collecte";
        $twig = View::getTwig();
        $content = $twig->render('Pages/collection_edit.twig', [
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
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
        return new Response($content);
    }
}
