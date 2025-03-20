<?php

// source\Views\Pages\Login.php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\Login\LoginController;
use Kouak\BackOfficeApp\Utilities\View;

class Login
{
    public static function render()
    {
        Session::startSession();
        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['csrf_token']) || !Session::verifyCsrfToken($_POST['csrf_token'])) {
                $error = "Le jeton CSRF est invalide. Veuillez réessayer.";
            } else {
                $email = $_POST["email"] ?? '';
                $password = $_POST["password"] ?? '';

                $pdo = Configuration::getPdo();
                $loginController = new LoginController($pdo);
                $user = $loginController->getUserByEmail($email, $password);

                if ($user && password_verify($password, $user['password'])) {
                    Session::regenerateSessionId();
                    Session::setSession("user_id", $user["volunteer_id"]);
                    Session::setSession("username", $user["username"]);
                    Session::setSession("role", $user["role"]);
                    Session::setSession("email", $user["email"]);
                    $baseUrl = Helpers::getBaseUrl();
                    header("Location: " . $baseUrl . "/collection-list");
                    exit;
                } else {
                    $error = "Identifiants incorrects";
                }
            }
        }
        $twig = View::getTwig();
        echo $twig->render('/Pages/login.twig', [
            'error'       => $error,
            'csrf_token'  => Session::getCsrfToken(),
            'session'     => $_SESSION,
        ]);
    }
}
