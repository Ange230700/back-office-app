<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Views\Components\HeadElement;
use Kouak\BackOfficeApp\Utilities\Helpers;
use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Database\Configuration;

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
            $email = $_POST["email"] ?? '';
            $password = $_POST["password"] ?? '';

            // Retrieve PDO instance from Configuration
            $pdo = Configuration::getPdo();

            $stmt = $pdo->prepare("SELECT * FROM benevoles WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Regenerate session ID to prevent fixation.
                Session::regenerate();

                // Set session variables.
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

        // Render the login form
        ob_start();
?>
        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <?php HeadElement::render(); ?>
            <title>Connexion</title>
        </head>

        <body class="bg-gray-100 text-gray-900">
            <div class="flex justify-center items-center min-h-screen">
                <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96">
                    <h1 class="text-3xl font-bold text-cyan-950 mb-6 text-center">Connexion</h1>
                    <?php if (!empty($error)) : ?>
                        <div class="text-red-600 text-center mb-4">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                            <input type="password" name="password" id="password" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex justify-between items-center">
                            <a href="/back-office-app/index.php?route=home" class="text-sm text-blue-600 hover:underline">Retour à la page d'accueil</a>
                            <button type="submit" class="bg-cyan-950 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow-md">
                                Se connecter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </body>

        </html>
<?php
        echo ob_get_clean();
    }
}
