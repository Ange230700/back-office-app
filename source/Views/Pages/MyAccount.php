<?php

// source\Views\Pages\MyAccount.php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\MyAccount\MyAccountController;
use Kouak\BackOfficeApp\Utilities\View;

class MyAccount
{
    public static function render()
    {
        Helpers::checkUserLoggedIn();
        $pdo = Configuration::getPdo();
        // $flash_success = Session::getSession("flash_success");
        // $flash_error = Session::getSession("flash_error");

        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");

        $controller = new MyAccountController($pdo);
        $userId = Session::getSession("user_id");
        $account = $controller->getAccount($userId);

        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['csrf_token']) || !Session::verifyCsrfToken($_POST['csrf_token'])) {
                $error = "Le jeton CSRF est invalide. Veuillez réessayer.";
            } else {
                $username = $_POST["username"] ?? "";
                $email = $_POST["email"] ?? "";
                $currentPassword = $_POST["current_password"] ?? "";
                $newPassword = $_POST["new_password"] ?? "";
                $confirmPassword = $_POST["confirm_password"] ?? "";

                $error = $controller->editAccount($userId, $username, $email, $currentPassword, $newPassword, $confirmPassword);
                if ($error === null) {
                    Session::setSession("username", $username);
                    Session::setSession("email", $email);
                    header("Location: /back-office-app/my-account");
                    exit;
                }
            }
        }

        $twig = View::getTwig();
        echo $twig->render('Pages/my_account.twig', [
            'account'   => $account,
            'error'     => $error,
            'session'   => $_SESSION,
            // 'flash_success' => $flash_success,
            // 'flash_error' => $flash_error,
        ]);
    }
}
