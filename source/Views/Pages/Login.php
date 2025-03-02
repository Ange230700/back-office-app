<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Login\LoginController;
use Kouak\BackOfficeApp\Utilities\View;

class Login
{
    /**
     * Render the login page and process form submission.
     */
    public static function render()
    {
        // Ensure session is started
        Session::start();

        $error = "";

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Check CSRF token before any further processing.
            if (!isset($_POST['csrf_token']) || !Session::verifyCsrfToken($_POST['csrf_token'])) {
                $error = "Le jeton CSRF est invalide. Veuillez réessayer.";
            } else {
                $email = $_POST["email"] ?? '';
                $password = $_POST["password"] ?? '';

                // Use LoginController to handle authentication.
                $pdo = Configuration::getPdo();
                $loginController = new LoginController($pdo);
                $user = $loginController->authenticate($email, $password);

                if ($user && password_verify($password, $user['mot_de_passe'])) {
                    Session::regenerate();
                    Session::set("user_id", $user["id"]);
                    Session::set("nom", $user["nom"]);
                    Session::set("role", $user["role"]);
                    Session::set("email", $user["email"]);
                    header("Location: /back-office-app/index.php?route=collection-list");
                    exit;
                } else {
                    $error = "Identifiants incorrects";
                }
            }
        }

        // Render the login form
        $twig = View::getTwig();
        echo $twig->render('login.twig', [
            'error'       => $error,
            'csrf_token'  => Session::getCsrfToken(),
            'session'     => $_SESSION, // pass session data to your template if needed
        ]);
    }
}
