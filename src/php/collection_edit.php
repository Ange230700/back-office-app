<?php
session_start();

$isUserLogged = isset($_SESSION["user_id"]);
if (!$isUserLogged) {
    header('Location: login.php');
    exit();
}

$isUserAdmin = $_SESSION["role"] === "admin";
if (!$isUserAdmin) {
    header("Location: collection_list.php");
    exit();
}

require 'config.php';

$dashboardRedirection = "Location: collection_list.php";
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header($dashboardRedirection);
    exit;
}

try {
    $sqlQueryToSelectCollectionToEdit = "SELECT id, date_collecte, lieu FROM collectes WHERE id = ?";
    $statementToGetCollectionToEdit = $pdo->prepare($sqlQueryToSelectCollectionToEdit);
    $collectionToEditId = $_GET['id'];
    if (!$statementToGetCollectionToEdit->execute([$collectionToEditId])) {
        die("Erreur lors de la récupération de la collecte.");
    }
    $collectionToEdit = $statementToGetCollectionToEdit->fetch();
    if (!$collectionToEdit) {
        header($dashboardRedirection);
        exit;
    }

    $sqlQueryToSelectVolunteersAssignedToCollection = "SELECT id_benevole FROM benevoles_collectes WHERE id_collecte = ?";
    $statementToGetVolunteersAssignedToCollection = $pdo->prepare($sqlQueryToSelectVolunteersAssignedToCollection);
    if (!$statementToGetVolunteersAssignedToCollection->execute([$collectionToEditId])) {
        die("Erreur lors de la récupération des bénévoles assignés à la collecte.");
    }
    $volunteersAssignedToCollection = $statementToGetVolunteersAssignedToCollection->fetchAll(PDO::FETCH_COLUMN);

    $sqlQueryToSelectVolunteersArray = "SELECT id, nom FROM benevoles ORDER BY nom";
    $statementToGetVolunteersArray = $pdo->prepare($sqlQueryToSelectVolunteersArray);
    if (!$statementToGetVolunteersArray->execute()) {
        die("Erreur lors de la récupération des bénévoles.");
    }
    $volunteersArray = $statementToGetVolunteersArray->fetchAll();

    $sqlQueryToSelectCollectedWasteDuringCollectionArray = "SELECT type_dechet, quantite_kg FROM dechets_collectes WHERE id_collecte = ?";
    $statementToGetCollectedWastesDuringCollectionArray = $pdo->prepare($sqlQueryToSelectCollectedWasteDuringCollectionArray);
    if (!$statementToGetCollectedWastesDuringCollectionArray->execute([$collectionToEditId])) {
        die("Erreur lors de la récupération des déchets collectés lors de la collecte.");
    }
    $collectedWastesDuringCollectionArray = $statementToGetCollectedWastesDuringCollectionArray->fetchAll();
    if (empty($collectedWastesDuringCollectionArray)) {
        $collectedWastesDuringCollectionArray[] = ['type_dechet' => '', 'quantite_kg' => ''];
    }

    $sqlQueryToSelectWasteTypesArray = "SELECT DISTINCT type_dechet FROM dechets_collectes";
    $statementToGetWasteTypesArray = $pdo->prepare($sqlQueryToSelectWasteTypesArray);
    if (!$statementToGetWasteTypesArray->execute()) {
        die("Erreur lors de la récupération des types de déchets collectés.");
    }
    $wasteTypesArray = $statementToGetWasteTypesArray->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $pdoException) {
    echo "Erreur de base de données : " . $pdoException->getMessage();
    exit;
}

$isFormSubmitted = $_SERVER["REQUEST_METHOD"] === "POST";
if ($isFormSubmitted) {
    $submittedDate = $_POST["date"];
    $submittedPlace = $_POST["lieu"];
    $volunteersAssignedToCollectionArray = isset($_POST['benevoles']) ? $_POST['benevoles'] : [];
    $wasteTypesArraySubmitted = isset($_POST['type_dechet']) ? $_POST['type_dechet'] : [];
    $quantitiesArraySubmitted = isset($_POST['quantite_kg']) ? $_POST['quantite_kg'] : [];

    try {
        $sqlQueryToUpdateCollection = "UPDATE collectes SET date_collecte = COALESCE(?, date_collecte), lieu = COALESCE(?, lieu) WHERE id = ?";
        $statementToUpdateCollection = $pdo->prepare($sqlQueryToUpdateCollection);
        if (!$statementToUpdateCollection->execute([$submittedDate, $submittedPlace, $collectionToEditId])) {
            die('Erreur lors de la mise à jour de la collecte.');
        }

        $sqlQueryToDeleteVolunteersAssignedToCollection = "DELETE FROM benevoles_collectes WHERE id_collecte = ?";
        $statementToDeleteVolunteersAssignedToCollection = $pdo->prepare($sqlQueryToDeleteVolunteersAssignedToCollection);
        if (!$statementToDeleteVolunteersAssignedToCollection->execute([$collectionToEditId])) {
            die('Erreur lors de la suppression des bénévoles assignés à la collecte.');
        }

        $sqlQueryToInsertVolunteersAssignedToCollection = "INSERT INTO benevoles_collectes (id_collecte, id_benevole) VALUES (?, ?)";
        $statementToAddVolunteersAssignedToCollection = $pdo->prepare($sqlQueryToInsertVolunteersAssignedToCollection);
        foreach ($volunteersAssignedToCollectionArray as $volunteerId) {
            if (!$statementToAddVolunteersAssignedToCollection->execute([$collectionToEditId, $volunteerId])) {
                die('Erreur lors de la mise à jour des bénévoles de la collecte.');
            }
        }

        $sqlQueryToDeleteCollectedWastesDuringCollection = "DELETE FROM dechets_collectes WHERE id_collecte = ?";
        $statementToDeleteCollectedWastesDuringCollection = $pdo->prepare($sqlQueryToDeleteCollectedWastesDuringCollection);
        $statementToDeleteCollectedWastesDuringCollection->execute([$collectionToEditId]);
        if (isset($_POST['type_dechet']) && isset($_POST['quantite_kg'])) {
            $stmtWaste = $pdo->prepare("INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)");
            $types = $_POST['type_dechet'];
            $quantities = $_POST['quantite_kg'];
            for ($i = 0; $i < count($types); $i++) {
                if (!empty($types[$i]) && is_numeric($quantities[$i])) {
                    $stmtWaste->execute([$collectionToEditId, $types[$i], $quantities[$i]]);
                }
            }
        }
    } catch (PDOException $pdoException) {
        echo "Erreur de base de données : " . $pdoException->getMessage();
        exit;
    }

    header($dashboardRedirection);
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require 'headElement.php'; ?>
    <title>Modifier une collecte</title>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <?php require 'navbar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-cyan-950 mb-6">Modifier une collecte</h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">
                            Date
                            <input type="date" id="date" name="date" value="<?= htmlspecialchars($collectionToEdit['date_collecte']) ?>" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </label>
                    </div>

                    <div>
                        <label for="lieu" class="block text-sm font-medium text-gray-700">
                            Lieu
                            <input type="text" id="lieu" name="lieu" value="<?= htmlspecialchars($collectionToEdit['lieu']) ?>" class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bénévoles
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <?php foreach ($volunteersArray as $benevole): ?>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="benevoles[]" value="<?= $benevole['id'] ?>" id="benevole_<?= $benevole['id'] ?>" class="mr-2" <?= in_array($benevole['id'], $volunteersAssignedToCollection) ? 'checked' : '' ?> />
                                        <label for="benevole_<?= $benevole['id'] ?>" class="text-gray-700">
                                            <?= htmlspecialchars($benevole['nom']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                        </label>
                    </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Déchets collectés
                    <div id="waste-container">
                        <?php foreach ($collectedWastesDuringCollectionArray as $item):
                            $selectOptions = "<option value=''>Sélectionner un type</option>";
                            foreach ($wasteTypesArray as $wasteType) {
                                $selected = ($wasteType == $item['type_dechet']) ? 'selected' : '';
                                $selectOptions .= "<option value='" . htmlspecialchars($wasteType) . "' $selected>" . htmlspecialchars($wasteType) . "</option>";
                            }
                        ?>
                            <div class="waste-item flex space-x-4 mb-2">
                                <select name="type_dechet[]" class="w-full p-2 border border-gray-300 rounded-lg">
                                    <?= $selectOptions ?>
                                </select>
                                <input type="number" min="0" step="0.1" name="quantite_kg[]" placeholder="Quantité (kg)" value="<?= htmlspecialchars($item['quantite_kg']) ?>" class="w-full p-2 border border-gray-300 rounded-lg">
                                <button type="button" class="bg-cyan-950 remove-waste hover:bg-red-600 text-white px-2 py-1 rounded">
                                    Supprimer
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" id="add-waste" class="bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mt-2">
                        Ajouter un déchet
                    </button>
                </label>
            </div>

            <?php
            $cancelUrl = "collection_list.php";
            $cancelTitle = "Retour à la liste des collectes";
            $buttonTitle = "Modifier la collecte";
            $buttonTextContent = "Modifier la collecte";
            require 'addEditButtons.php';
            ?>
            </form>
    </div>
    </div>
    </div>
    <script>
        const wasteRowTemplate = `
        <div class="waste-item flex space-x-4 mb-2">
        <select name="type_dechet[]" class="w-full p-2 border border-gray-300 rounded-lg">
        <option value="">Sélectionner un type</option>
        <?php foreach ($wasteTypesArray as $wasteType): ?>
                        <option value="<?= htmlspecialchars($wasteType) ?>"><?= htmlspecialchars($wasteType) ?></option>
                    <?php endforeach; ?>
                    </select>
                    <input type="number" step="0.1" name="quantite_kg[]" placeholder="Quantité (kg)" class="w-full p-2 border border-gray-300 rounded-lg">
                <button type="button" class="bg-cyan-950 remove-waste hover:bg-red-600 text-white px-2 py-1 rounded">
                    Supprimer
                </button>
            </div>
            `;

        function updateWasteSelectOptions() {
            const selects = document.querySelectorAll("select[name='type_dechet[]']");
            const selectedValues = Array.from(selects)
                .map(select => select.value)
                .filter(value => value !== "");

            selects.forEach(select => {
                select.querySelectorAll("option").forEach(option => {
                    if (selectedValues.includes(option.value) && option.value !== select.value) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        document.getElementById('waste-container').addEventListener('change', function(e) {
            if (e.target && e.target.matches("select[name='type_dechet[]']")) {
                updateWasteSelectOptions();
            }
        });

        document.getElementById('add-waste').addEventListener('click', function() {
            const container = document.getElementById('waste-container');
            container.insertAdjacentHTML('beforeend', wasteRowTemplate);
            updateWasteSelectOptions();
        });

        document.getElementById('waste-container').addEventListener('click', function(e) {
            if (e.target && e.target.matches('button.remove-waste')) {
                e.target.parentNode.remove();
                updateWasteSelectOptions();
            }
        });

        updateWasteSelectOptions();
    </script>
</body>

</html>