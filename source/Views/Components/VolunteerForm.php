<?php

namespace Kouak\BackOfficeApp\Views\Components;

use Kouak\BackOfficeApp\Utilities\Session;

class VolunteerForm
{
    /**
     * Render the volunteer add/edit form.
     *
     * Expected keys in $data:
     * - actionUrl: string (form action)
     * - cancelUrl: string (URL for cancel button)
     * - cancelTitle: string
     * - buttonTitle: string
     * - buttonTextContent: string
     * - volunteer: array (if editing; empty for add)
     * - collectionsList: array (list of collections for participation)
     * - selectedCollections: array (IDs of collections the volunteer participates in)
     * - error: string (optional error message)
     */
    public static function render(array $data)
    {
        $actionUrl = $data['actionUrl'];
        $cancelUrl = $data['cancelUrl'];
        $cancelTitle = $data['cancelTitle'];
        $buttonTitle = $data['buttonTitle'];
        $buttonTextContent = $data['buttonTextContent'];
        $volunteer = $data['volunteer'];
        $collectionsList = $data['collectionsList'];
        $selectedCollections = $data['selectedCollections'];
        $error = $data['error'] ?? '';

        // Role may already be set if editing.
        $roleValue = isset($volunteer['role']) ? $volunteer['role'] : 'participant';
?>
        <?php if ($error): ?>
            <div class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-4" action="<?= htmlspecialchars($actionUrl) ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
            <?php if (empty($volunteer)): ?>
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">
                        Nom
                        <input type="text" id="nom" name="nom" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nom du bénévole" required>
                    </label>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email
                        <input type="email" id="email" name="email" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Email du bénévole" required>
                    </label>
                </div>
                <div>
                    <label for="mot_de_passe" class="block text-sm font-medium text-gray-700">
                        Mot de passe
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Mot de passe" required>
                    </label>
                </div>
            <?php endif; ?>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    Rôle
                    <select id="role" name="role" class="w-full mt-2 p-3 border border-gray-300 rounded-lg" required>
                        <option value="participant" <?= ($roleValue === 'participant') ? 'selected' : '' ?>>Participant</option>
                        <option value="admin" <?= ($roleValue === 'admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Participations
                    <?php if (!empty($collectionsList)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <?php foreach ($collectionsList as $collection): ?>
                                <div class="flex items-center">
                                    <input type="checkbox" name="attendances[]" value="<?= $collection['id'] ?>" id="collection_<?= $collection['id'] ?>" class="mr-2"
                                        <?= in_array($collection['id'], $selectedCollections) ? 'checked' : '' ?>>
                                    <label for="collection_<?= $collection['id'] ?>" class="text-gray-700">
                                        <?= htmlspecialchars($collection['collection_label']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500">Aucune collecte n'est disponible pour l'instant.</p>
                    <?php endif; ?>
                </label>
            </div>
            <?php AddOrEditButtonsGroup::render($cancelUrl, $cancelTitle, $buttonTitle, $buttonTextContent); ?>
        </form>
<?php
    }
}
