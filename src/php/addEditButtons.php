<?php
$cancelButtonTailwindStyles = "bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow";
$submitButtonTailwindStyles = "bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md font-semibold";
?>

<div class="flex justify-end space-x-4">
    <a href="<?= $cancelUrl ?>" class="<?= $cancelButtonTailwindStyles ?>" title="<?= $cancelTitle ?>">
        Annuler
    </a>
    <button type="submit" class="<?= $submitButtonTailwindStyles ?>" title="<?= $buttonTitle ?>">
        <?= $buttonTextContent ?>
    </button>
</div>