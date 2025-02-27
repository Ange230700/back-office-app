<?php

namespace Kouak\BackOfficeApp\Views\Components;

class ActionButtons
{
    /**
     * Render the action buttons (Edit and Delete).
     *
     * @param string $editUrl                URL for the edit action.
     * @param string $deleteUrl              URL for the delete action.
     * @param string $editTitle              Title (tooltip) for the edit button.
     * @param string $deleteTitle            Title (tooltip) for the delete button.
     * @param string $editButtonTailwindStyles CSS classes for the edit button.
     * @param string $deleteButtonTailwindStyles CSS classes for the delete button.
     */
    public static function render($editUrl, $deleteUrl, $editTitle, $deleteTitle, $editButtonTailwindStyles = "bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200", $deleteButtonTailwindStyles = "bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition duration-200")
    {
        ob_start();
?>
        <div class="flex space-x-2">
            <a href="<?= $editUrl ?>"
                class="<?= $editButtonTailwindStyles ?>"
                title="<?= $editTitle ?>">
                Modifier
            </a>
            <a href="<?= $deleteUrl ?>"
                class="<?= $deleteButtonTailwindStyles ?>"
                title="<?= $deleteTitle ?>"
                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?');">
                Supprimer
            </a>
        </div>
<?php
        echo ob_get_clean();
    }
}
