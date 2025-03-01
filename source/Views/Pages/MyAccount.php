<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Database\Configuration;
use Kouak\BackOfficeApp\Controllers\MyAccount\MyAccountController;
use Kouak\BackOfficeApp\Views\Components\HeadElement;
use Kouak\BackOfficeApp\Views\Components\Navbar;

class MyAccount
{
    public static function render()
    {
        Helpers::checkUserLoggedIn();
        $pdo = Configuration::getPdo();

        $controller = new MyAccountController($pdo);
        $userId = Session::get("user_id");
        $account = $controller->getAccount($userId);

        $error = "";
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $nom = $_POST["nom"] ?? "";
            $email = $_POST["email"] ?? "";
            $currentPassword = $_POST["current_password"] ?? "";
            $newPassword = $_POST["new_password"] ?? "";
            $confirmPassword = $_POST["confirm_password"] ?? "";

            $error = $controller->updateAccount($userId, $nom, $email, $currentPassword, $newPassword, $confirmPassword);
            if ($error === null) {
                // Update session values
                Session::set("nom", $nom);
                Session::set("email", $email);
                // Optionally, redirect to a confirmation page or simply continue.
                header("Location: /back-office-app/index.php?route=my-account");
                exit;
            }
        }

        // Set up page variables.
        $pageTitle = "Mon compte";
        $pageHeader = "Mon compte";

        // Build the HTML form content.
        ob_start();
?>
        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <?php HeadElement::render(); ?>
            <title><?= $pageHeader ?></title>
        </head>

        <body class="bg-gray-100 text-gray-900">
            <div class="flex h-screen">
                <?php Navbar::render(); ?>
                <main class="flex-1 p-8 overflow-y-auto">
                    <h1 class="text-4xl text-cyan-950 font-bold mb-6"><?= $pageTitle ?></h1>
                    <?php if (!empty($error)): ?>
                        <div class="text-red-600 text-center mb-4"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($account['nom']) ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($account['email']) ?>" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">
                                Mot de passe actuel
                            </label>
                            <input type="password" name="current_password" id="current_password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700">
                                Nouveau mot de passe
                            </label>
                            <input type="password" name="new_password" id="new_password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                                Confirmer le mot de passe
                            </label>
                            <input type="password" name="confirm_password" id="confirm_password" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex justify-between items-center">
                            <a href="/back-office-app/index.php?route=collection-list" class="text-sm text-blue-600 hover:underline">
                                Retour à la liste des collectes
                            </a>
                            <button type="submit" class="bg-cyan-950 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow-md">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </main>
            </div>
        </body>

        </html>
<?php
        $content = ob_get_clean();
        // Optionally, you can use your Main layout here as well:
        echo $content;
    }
}
