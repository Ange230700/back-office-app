<?php
$editButtonTailwindStyles = "bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200";
$deleteButtonTailwindStyles = "bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200";
?>
<td class="py-3 px-4 flex space-x-2">
    <a href="<?= $editUrl ?>"
        class="<?= $editButtonTailwindStyles ?>"
        title="<?= $editTitle ?>">
        Modifier
    </a>
    <!-- TODO: Replace the confirmation message with a dialog box done with a CSS framework or library -->
    <a href="<?= $deleteUrl ?>"
        class="<?= $deleteButtonTailwindStyles ?>"
        title="<?= $deleteTitle ?>"
        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?');">
        Supprimer
    </a>
</td>