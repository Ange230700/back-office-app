<?php
if (!isset($tableHeadersRow) || !isset($tableBody)) {
    die("Table headers and body are not set.");
}
?>
<div class="overflow-hidden rounded-lg shadow-lg bg-white">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-cyan-950 text-white">
            <?= $tableHeadersRow ?>
        </thead>
        <tbody class="divide-y divide-gray-300">
            <?= $tableBody ?>
        </tbody>
    </table>
</div>