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
            <?php if ($pageTitle === "Liste des Collectes"): ?>
                <!-- Statistics Section -->
                <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des Collectes</h3>
                        <p class="text-3xl font-bold text-blue-600"><?= $totalPages * getPaginationParams()['limit'] /* or use total from count query */ ?></p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des déchets collectés</h3>
                        <p class="text-3xl font-bold text-blue-600"><?= $collectedWastesTotalQuantity ?> kg</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Dernière Collecte</h3>
                        <?php if ($mostRecentCollection): ?>
                            <p class="text-lg text-gray-600"><?= htmlspecialchars($mostRecentCollection['lieu']) ?></p>
                            <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($mostRecentCollection['date_collecte'])) ?></p>
                        <?php else: ?>
                            <p class="text-lg text-gray-600">Aucune collecte pour le moment</p>
                        <?php endif; ?>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Prochaine Collecte</h3>
                        <?php if ($nextCollection): ?>
                            <p class="text-lg text-gray-600"><?= $nextCollection['lieu'] ?></p>
                            <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($nextCollection['date_collecte'])) ?></p>
                        <?php else: ?>
                            <p class="text-lg text-gray-600">Aucune collecte à venir</p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
            <h1 class="text-4xl font-bold text-cyan-950 mb-6"><?= $pageHeader ?></h1>
            <div>
                <?= $content ?>
            </div>
        </main>
    </div>
</body>

</html>