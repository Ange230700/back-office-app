<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require 'headElement.php'; ?>
    <title><?= $pageTitle ?></title>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <?php require 'navbar.php'; ?>
        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-cyan-950 mb-6"><?= $pageHeader ?></h1>
            <div>
                <?= $content ?>
            </div>
        </main>
    </div>
</body>

</html>