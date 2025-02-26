<?php
$paginationButtonsTailwindBaseStyles = "min-w-[120px] text-center bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md transition";
?>

<div class="flex justify-center items-center space-x-4 mt-4">
    <?php if ($totalPages >= 1): ?>
    <a href="?pageNumber=<?= max(1, $pageNumber - 1) ?>"
        class="<?= $paginationButtonsTailwindBaseStyles ?> <?= ($pageNumber <= 1) ? 'pointer-events-none opacity-50' : '' ?>">
        Précédent
    </a>
    <span class="text-gray-700 font-semibold">
        Page <?= $pageNumber ?> sur <?= $totalPages ?>
    </span>
    <a href="?pageNumber=<?= min($totalPages, $pageNumber + 1) ?>"
        class="<?= $paginationButtonsTailwindBaseStyles ?> <?= ($pageNumber >= $totalPages) ? 'pointer-events-none opacity-50' : '' ?>">
        Suivant
    </a>
    <?php else: ?>
    <span class="text-gray-700 font-semibold">Aucune donnée à afficher</span>
    <?php endif; ?>
</div>