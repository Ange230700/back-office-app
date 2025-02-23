<?php
$dateValue  = isset($collection['date_collecte']) ? htmlspecialchars($collection['date_collecte']) : '';
$lieuValue  = isset($collection['lieu']) ? htmlspecialchars($collection['lieu']) : '';
$selectedVolunteers = isset($selectedVolunteers) ? $selectedVolunteers : [];
if (!isset($collectedWastes) || empty($collectedWastes)) {
    $collectedWastes = [['type_dechet' => '', 'quantite_kg' => '']];
}

function buildWasteSelectOptions($wasteTypes, $selected = '')
{
    $options = "<option value=''>Sélectionner un type</option>";
    foreach ($wasteTypes as $wasteType) {
        $sel = ($wasteType === $selected) ? 'selected' : '';
        $options .= "<option value='" . htmlspecialchars($wasteType) . "' $sel>" . htmlspecialchars($wasteType) . "</option>";
    }
    return $options;
}

$defaultWasteOptions = buildWasteSelectOptions($wasteTypes);
?>

<form method="POST" class="space-y-4" action="<?= $actionUrl ?>">
    <div>
        <label for="date" class="block text-sm font-medium text-gray-700">
            Date
            <input type="date" id="date" name="date" value="<?= $dateValue ?>" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
        </label>
    </div>
    <div>
        <label for="lieu" class="block text-sm font-medium text-gray-700">
            Lieu
            <input type="text" id="lieu" name="lieu" value="<?= $lieuValue ?>" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Lieu de la collecte" required />
        </label>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Bénévoles
            <?php if (!empty($volunteers)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <?php foreach ($volunteers as $volunteer): ?>
                        <div class="flex items-center">
                            <input type="checkbox" name="benevoles[]" value="<?= $volunteer['id'] ?>" id="benevole_<?= $volunteer['id'] ?>" class="mr-2"
                                <?= in_array($volunteer['id'], $selectedVolunteers) ? 'checked' : '' ?> />
                            <label for="benevole_<?= $volunteer['id'] ?>" class="text-gray-700"><?= htmlspecialchars($volunteer['nom']) ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">Aucun bénévole n'est disponible pour l'instant.</p>
            <?php endif; ?>
        </label>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Déchets collectés
            <div id="waste-container" data-options="<?= htmlspecialchars($defaultWasteOptions) ?>">
                <?php foreach ($collectedWastes as $waste):
                    $options = buildWasteSelectOptions($wasteTypes, $waste['type_dechet']);
                ?>
                    <div class="waste-item flex space-x-4 mb-2">
                        <select name="type_dechet[]" class="w-full p-2 border border-gray-300 rounded-lg">
                            <?= $options ?>
                        </select>
                        <input type="number" min="0" step="0.1" name="quantite_kg[]" placeholder="Quantité (kg)" value="<?= htmlspecialchars($waste['quantite_kg']) ?>" class="w-full p-2 border border-gray-300 rounded-lg" />
                        <button type="button" class="bg-cyan-950 remove-waste hover:bg-red-600 text-white px-2 py-1 rounded">
                            Supprimer
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-waste" class="bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mt-2">Ajouter un déchet</button>
        </label>
    </div>
    <?php
    require 'addEditButtons.php';
    ?>
</form>
<script src="collection.js"></script>