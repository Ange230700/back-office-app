<?php

namespace Kouak\BackOfficeApp\Views\Components;

use Kouak\BackOfficeApp\Utilities\Session;
use Kouak\BackOfficeApp\Views\Components\AddOrEditButtonsGroup;

class CollectionForm
{
    /**
     * Render the collection form.
     *
     * Expected keys in the $data array:
     * - actionUrl: string
     * - cancelUrl: string
     * - cancelTitle: string
     * - buttonTitle: string
     * - buttonTextContent: string
     * - volunteersList: array (list of volunteers)
     * - wasteTypesList: array (list of waste types)
     * - placesList: array (list of places)
     * - collection: array (existing collection data, empty for add)
     * - selectedVolunteersList: array
     * - collectedWastesList: array
     * - error: string (optional)
     */
    public static function render(array $data)
    {
        // Extract variables
        $actionUrl = $data['actionUrl'];
        $cancelUrl = $data['cancelUrl'];
        $cancelTitle = $data['cancelTitle'];
        $buttonTitle = $data['buttonTitle'];
        $buttonTextContent = $data['buttonTextContent'];
        $volunteersList = $data['volunteersList'];
        $wasteTypesList = $data['wasteTypesList'];
        $placesList = $data['placesList'];
        $collection = $data['collection'];
        $selectedVolunteersList = $data['selectedVolunteersList'];
        $collectedWastesList = $data['collectedWastesList'];
        $error = $data['error'] ?? '';

        $dateValue = isset($collection['date_collecte']) ? htmlspecialchars($collection['date_collecte']) : '';
        $lieuValue = isset($collection['lieu']) ? htmlspecialchars($collection['lieu']) : '';

        // Build default waste options
        $defaultWasteOptions = self::buildWasteSelectOptions($wasteTypesList);
        if (!isset($collectedWastesList) || empty($collectedWastesList)) {
            $collectedWastesList = [['type_dechet' => '', 'quantite_kg' => '']];
        }
?>
        <?php if ($error): ?>
            <div class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-4" action="<?= htmlspecialchars($actionUrl) ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700">
                    Date
                    <input type="date" id="date" name="date" value="<?= $dateValue ?>" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                </label>
            </div>
            <div>
                <label for="lieu" class="block text-sm font-medium text-gray-700">
                    Lieu
                    <input type="text" id="lieu" name="lieu" value="<?= $lieuValue ?>" list="lieuxList" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Lieu de la collecte" required />
                </label>
                <datalist id="lieuxList">
                    <?php foreach ($placesList as $place): ?>
                        <option value="<?= htmlspecialchars($place) ?>">
                        <?php endforeach; ?>
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Bénévoles
                    <?php if (!empty($volunteersList)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2">
                            <?php foreach ($volunteersList as $volunteer): ?>
                                <div class="flex items-center">
                                    <input type="checkbox" name="benevoles[]" value="<?= $volunteer['id'] ?>" id="benevole_<?= $volunteer['id'] ?>" class="mr-2"
                                        <?= in_array($volunteer['id'], $selectedVolunteersList) ? 'checked' : '' ?> />
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
                    <div id="waste-container" class="mt-2" data-options="<?= htmlspecialchars($defaultWasteOptions) ?>">
                        <?php foreach ($collectedWastesList as $waste): ?>
                            <div class="waste-item flex space-x-4 mb-2">
                                <input type="text" name="type_dechet[]" list="wasteTypesList" value="<?= htmlspecialchars($waste['type_dechet']) ?>" placeholder="Sélectionner ou saisir un nouveau type" class="w-full p-2 border border-gray-300 rounded-lg">
                                <input type="number" min="0" step="0.1" name="quantite_kg[]" placeholder="Quantité (kg)" value="<?= htmlspecialchars($waste['quantite_kg']) ?>" class="w-full p-2 border border-gray-300 rounded-lg" />
                                <button type="button" class="bg-cyan-950 remove-waste hover:bg-red-600 text-white px-2 py-1 rounded">
                                    Supprimer
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <datalist id="wasteTypesList">
                        <?php foreach ($wasteTypesList as $wasteType): ?>
                            <option value="<?= htmlspecialchars($wasteType) ?>">
                            <?php endforeach; ?>
                    </datalist>
                    <button type="button" id="add-waste" class="bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mt-2">Ajouter un déchet</button>
                </label>
            </div>
            <?php AddOrEditButtonsGroup::render($cancelUrl, $cancelTitle, $buttonTitle, $buttonTextContent); ?>
        </form>
        <script src="/back-office-app/source/Views/javascript/collection.js"></script>
<?php
    }

    private static function buildWasteSelectOptions($wasteTypesList, $selected = '')
    {
        $options = "<option value=''>Sélectionner un type</option>";
        foreach ($wasteTypesList as $wasteType) {
            $sel = ($wasteType === $selected) ? 'selected' : '';
            $options .= "<option value='" . htmlspecialchars($wasteType) . "' $sel>" . htmlspecialchars($wasteType) . "</option>";
        }
        return $options;
    }
}
