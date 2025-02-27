<?php

namespace Kouak\BackOfficeApp\Views\Components;

class PaginationButtons
{
    /**
     * Render the pagination buttons.
     *
     * @param int    $totalPages Total number of pages.
     * @param int    $pageNumber Current page number (if null, uses $_GET['pageNumber'] or defaults to 1).
     * @param string $route      Optional route parameter (e.g., 'volunteer-list' or 'collection-list').
     * @param string $baseStyles CSS classes for the buttons.
     */
    public static function render($totalPages, $pageNumber = null, $route = '', $baseStyles = "min-w-[120px] text-center bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md transition")
    {
        if ($pageNumber === null) {
            $pageNumber = isset($_GET['pageNumber']) ? (int)$_GET['pageNumber'] : 1;
        }
        // Build URL prefix. If $route is provided, include it.
        $urlPrefix = $route ? "?route=" . urlencode($route) . "&" : "?";
        ob_start();
?>
        <div class="flex justify-center items-center space-x-4 mt-4">
            <?php if ($totalPages >= 1): ?>
                <a href="<?= $urlPrefix . "pageNumber=" . max(1, $pageNumber - 1) ?>"
                    class="<?= $baseStyles ?> <?= ($pageNumber <= 1) ? 'pointer-events-none opacity-50' : '' ?>">
                    Précédent
                </a>
                <span class="text-gray-700 font-semibold">
                    Page <?= $pageNumber ?> sur <?= $totalPages ?>
                </span>
                <a href="<?= $urlPrefix . "pageNumber=" . min($totalPages, $pageNumber + 1) ?>"
                    class="<?= $baseStyles ?> <?= ($pageNumber >= $totalPages) ? 'pointer-events-none opacity-50' : '' ?>">
                    Suivant
                </a>
            <?php else: ?>
                <span class="text-gray-700 font-semibold">Aucune donnée à afficher</span>
            <?php endif; ?>
        </div>
<?php
        echo ob_get_clean();
    }
}
