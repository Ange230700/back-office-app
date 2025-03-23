<?php

// source\Views\Pages\MyAccount.php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\MyAccount\MyAccountController;
use Kouak\BackOfficeApp\Utilities\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Kouak\BackOfficeApp\Utilities\UrlGenerator;

class MyAccount
{
    public static function render()
    {
        Helpers::checkUserLoggedIn();
        $email = Session::getSession("email");
        $demoUsers = ['admin@admin.admin', 'user@user.user'];
        if (in_array($email, $demoUsers)) {
            Session::setSession("flash_error", "Accès refusé pour ce compte.");
            return new RedirectResponse(UrlGenerator::generate('collection-list'));
        }
        $pdo = Configuration::getPdo();
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
                    return new RedirectResponse(UrlGenerator::generate("my-account"));
                }
            }
        }
        $twig = View::getTwig();
        $content = $twig->render('Pages/my_account.twig', [
            'account'   => $account,
            'error'     => $error,
            'session'   => $_SESSION,
        ]);
        Session::removeSessionVariable("flash_success");
        Session::removeSessionVariable("flash_error");
        return new Response($content);
    }
}
