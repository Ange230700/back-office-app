<form method="POST" class="space-y-4">
    <?php if (!$isEdit): ?>
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
                <option value="participant" <?= (isset($volunteer) && $volunteer['role'] === 'participant') ? 'selected' : '' ?>>Participant</option>
                <option value="admin" <?= (isset($volunteer) && $volunteer['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
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
    <?php
    require 'addEditButtons.php';
    ?>
</form>