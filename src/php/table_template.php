<?php
if (!isset($headerHtml) || !isset($bodyHtml)) {
    die("Table headers and body are not set.");
}
?>
<div class="overflow-hidden rounded-lg shadow-lg bg-white">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-cyan-950 text-white">
            <?= $headerHtml ?>
        </thead>
        <tbody class="divide-y divide-gray-300">
            <?= $bodyHtml ?>
        </tbody>
    </table>
</div>