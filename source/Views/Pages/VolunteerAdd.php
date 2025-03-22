<?php

// source\Views\Pages\VolunteerAdd.php

namespace Kouak\BackOfficeApp\Views\Pages;

use PDOException;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Volunteer\VolunteerController;
use Kouak\BackOfficeApp\Controllers\CollectionEvent\CollectionController;
use Kouak\BackOfficeApp\Utilities\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class VolunteerAdd
{
    public static function render()
    {
        Helpers::checkUserAdmin();
        $pdo = Configuration::getPdo();
        $volunteerController = new VolunteerController($pdo);
        $collectionController = new CollectionController($pdo);
        $collectionsList = $collectionController->getCollectionsList();
        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['csrf_token']) || !Session::verifyCsrfToken($_POST['csrf_token'])) {
                $error = "Le jeton CSRF est invalide. Veuillez réessayer.";
            } else {
                $submittedName = $_POST['username'] ?? '';
                $submittedEmail = $_POST['email'] ?? '';
                $submittedPassword = $_POST['password'] ?? '';
                $hashedPassword = password_hash($submittedPassword, PASSWORD_DEFAULT);
                $submittedRole = $_POST['role'] ?? 'participant';
                $submittedParticipations = $_POST['attendances'] ?? [];
                try {
                    $volunteerController->addVolunteer($submittedName, $submittedEmail, $hashedPassword, $submittedRole, $submittedParticipations);
                    return new RedirectResponse("/back-office-app/public/volunteer-list");
                } catch (PDOException $e) {
                    $error = "Erreur de base de données : " . $e->getMessage();
                }
            }
        }
        $actionUrl = $_SERVER['PHP_SELF'] . "/volunteer-add";
        $cancelUrl = "volunteer-list";
        $cancelTitle = "Retour à la liste des bénévoles";
        $buttonTitle = "Ajouter le bénévole";
        $buttonTextContent = "Ajouter le bénévole";
        $volunteer = [];
        $selectedCollections = [];
        $twig = View::getTwig();
        $content = $twig->render('Pages/volunteer_add.twig', [
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
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
        return new Response($content);
    }
}
