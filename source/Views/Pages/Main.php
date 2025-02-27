<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Views\Components\HeadElement;
use Kouak\BackOfficeApp\Views\Components\Navbar;
use Kouak\BackOfficeApp\Utilities\Helpers;

class Main
{
    /**
     * Render the dashboard section for the "Liste des Collectes" page.
     *
     * Expects the following keys in $data (if available):
     * - totalPages
     * - collectedWastesTotalQuantity
     * - mostRecentCollection
     * - nextCollection
     * - dateFormat (defaults to "d/m/Y")
     *
     * @param array $data
     */
    private static function renderDashboardSection(array $data)
    {
        $totalPages                   = $data['totalPages'] ?? null;
        $collectedWastesTotalQuantity = $data['collectedWastesTotalQuantity'] ?? null;
        $mostRecentCollection         = $data['mostRecentCollection'] ?? null;
        $nextCollection               = $data['nextCollection'] ?? null;
        $dateFormat                   = $data['dateFormat'] ?? "d/m/Y";
?>
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des Collectes</h3>
                <p class="text-3xl font-bold text-blue-600">
                    <?= isset($totalPages) ? ($totalPages * Helpers::getPaginationParams()['limit']) : '0' ?>
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Total des déchets collectés</h3>
                <p class="text-3xl font-bold text-blue-600">
                    <?= isset($collectedWastesTotalQuantity) ? $collectedWastesTotalQuantity : '0' ?> kg
                </p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Dernière Collecte</h3>
                <?php if (isset($mostRecentCollection) && $mostRecentCollection): ?>
                    <p class="text-lg text-gray-600"><?= htmlspecialchars($mostRecentCollection['lieu']) ?></p>
                    <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($mostRecentCollection['date_collecte'])) ?></p>
                <?php else: ?>
                    <p class="text-lg text-gray-600">Aucune collecte pour le moment</p>
                <?php endif; ?>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Prochaine Collecte</h3>
                <?php if (isset($nextCollection) && $nextCollection): ?>
                    <p class="text-lg text-gray-600"><?= htmlspecialchars($nextCollection['lieu']) ?></p>
                    <p class="text-lg text-gray-600"><?= date($dateFormat, strtotime($nextCollection['date_collecte'])) ?></p>
                <?php else: ?>
                    <p class="text-lg text-gray-600">Aucune collecte à venir</p>
                <?php endif; ?>
            </div>
        </section>
    <?php
    }

    /**
     * Render the main layout.
     *
     * @param string $pageTitle       The title of the page.
     * @param string $pageHeader      The header of the page.
     * @param string $content         The main content.
     * @param array  $dashboardData   Optional dashboard data for "Liste des Collectes" page.
     */
    public static function render($pageTitle, $pageHeader, $content, array $dashboardData = [])
    {
        ob_start();
    ?>
        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <?php HeadElement::render(); ?>
            <title><?= htmlspecialchars($pageTitle) ?></title>
        </head>

        <body class="bg-gray-100 text-gray-900">
            <div class="flex h-screen">
                <?php Navbar::render(); ?>
                <main class="flex-1 p-8 overflow-y-auto">
                    <?php
                    if ($pageTitle === "Liste des Collectes") {
                        self::renderDashboardSection($dashboardData);
                    }
                    ?>
                    <h1 class="text-4xl font-bold text-cyan-950 mb-6"><?= htmlspecialchars($pageHeader) ?></h1>
                    <div>
                        <?= $content ?>
                    </div>
                </main>
            </div>
            <script src="../javascript/collection.js"></script>
        </body>

        </html>
<?php
        echo ob_get_clean();
    }
}
