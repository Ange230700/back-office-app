<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header('Location: login.php');
    exit();
}
require "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $email = $_POST["email"];
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
        $stmt = $pdo->prepare("SELECT mot_de_passe FROM benevoles WHERE id = ?");
        $stmt->execute([$_SESSION["user_id"]]);
        $user = $stmt->fetch();

        if ($user && password_verify($currentPassword, $user['mot_de_passe'])) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmtUpdatePassword = $pdo->prepare("UPDATE benevoles SET mot_de_passe = ? WHERE id = ?");
                $stmtUpdatePassword->execute([$hashedPassword, $_SESSION["user_id"]]);
            } else {
                $error = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
            }
        } else {
            $error = "Le mot de passe actuel est incorrect.";
        }
    }
    $stmtUpdate = $pdo->prepare("UPDATE benevoles SET nom = COALESCE(?, nom), email = COALESCE(?, email)
     WHERE id = ?");
    $stmtUpdate->execute([$nom, $email, $_SESSION["user_id"]]);

    $_SESSION["nom"] = $nom;
    $_SESSION["email"] = $email;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require 'headElement.php'; ?>
    <title>Mon compte</title>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <?php require 'navbar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl text-cyan-950 font-bold mb-6">Mon compte</h1>

            <?php if (!empty($error)) : ?>
                <div class="text-red-600 text-center mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form id="settings-form" method="POST" class="space-y-6">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">nom</label>
                    <input type="nom" name="nom" id="nom" value="<?= !isset($nom) ? htmlspecialchars($_SESSION['nom']) : $nom ?>"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="<?= !isset($nom) ? htmlspecialchars($_SESSION['email']) : $email ?>"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">
                        Mot de passe actuel
                    </label>
                    <input type="password" name="current_password" id="current_password"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">
                        Nouveau mot de passe
                    </label>
                    <input type="password" name="new_password" id="new_password"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                        Confirmer le mot de passe
                    </label>
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" />
                </div>

                <div class="flex justify-between items-center">
                    <a href="collection_list.php" class="text-sm text-blue-600 hover:underline">
                        Retour à la liste des collectes
                    </a>
                    <button type="submit"
                        class="bg-cyan-950 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow-md">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </main>
    </div>
</body>

</html>