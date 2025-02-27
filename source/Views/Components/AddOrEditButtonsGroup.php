<?php

namespace Kouak\BackOfficeApp\Views\Components;

class AddOrEditButtonsGroup
{
    /**
     * Render the cancel and submit buttons.
     *
     * @param string $cancelUrl         URL to cancel and return.
     * @param string $cancelTitle       Tooltip for the cancel button.
     * @param string $buttonTitle       Tooltip for the submit button.
     * @param string $buttonTextContent Text for the submit button.
     */
    public static function render($cancelUrl, $cancelTitle, $buttonTitle, $buttonTextContent)
    {
        // Assume you have defined your CSS classes somewhere (or inline them)
        $cancelButtonTailwindStyles = "bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow";
        $submitButtonTailwindStyles = "bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md font-semibold";
?>
        <div class="flex justify-end space-x-4">
            <a href="<?= htmlspecialchars($cancelUrl) ?>" class="<?= $cancelButtonTailwindStyles ?>" title="<?= htmlspecialchars($cancelTitle) ?>">
                Annuler
            </a>
            <button type="submit" class="<?= $submitButtonTailwindStyles ?>" title="<?= htmlspecialchars($buttonTitle) ?>">
                <?= htmlspecialchars($buttonTextContent) ?>
            </button>
        </div>
<?php
    }
}
