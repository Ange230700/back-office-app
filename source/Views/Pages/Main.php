<?php

namespace Kouak\BackOfficeApp\Views\Pages;

use Kouak\BackOfficeApp\Utilities\Helpers;

class Main
{
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
}
