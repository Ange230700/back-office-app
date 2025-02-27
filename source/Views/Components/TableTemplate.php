<?php

namespace Kouak\BackOfficeApp\Views\Components;

class TableTemplate
{
    /**
     * Render a table given the headers and body content.
     *
     * @param string $tableHeadersRow The HTML for the table header row.
     * @param string $tableBody       The HTML for the table body.
     */
    public static function render($tableHeadersRow, $tableBody)
    {
        if (!isset($tableHeadersRow) || !isset($tableBody)) {
            die("Table headers and body are not set.");
        }
        ob_start();
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
<?php
        echo ob_get_clean();
    }
}
