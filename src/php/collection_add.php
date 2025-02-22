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

try {
    $sqlQueryToSelectVolunteersArray = "SELECT id, nom FROM benevoles ORDER BY nom";
    $statementToGetVolunteersArray = $pdo->prepare($sqlQueryToSelectVolunteersArray);
    if (!$statementToGetVolunteersArray->execute()) {
        die("Erreur lors de la récupération des bénévoles.");
    }
    $volunteersArray = $statementToGetVolunteersArray->fetchAll();

    $sqlQueryToSelectCollectedWastesArray = "SELECT DISTINCT type_dechet FROM dechets_collectes";
    $statementToGetCollectedWastesArray = $pdo->prepare($sqlQueryToSelectCollectedWastesArray);
    if (!$statementToGetCollectedWastesArray->execute()) {
        die("Erreur lors de la récupération des types de déchets collectés.");
    }
    $collectedWastesArray = $statementToGetCollectedWastesArray->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $pdoException) {
    echo "Erreur de base de données : " . $pdoException->getMessage();
    exit;
}

$isFormSubmitted = $_SERVER["REQUEST_METHOD"] === "POST";
if ($isFormSubmitted) {
    $submittedDate = $_POST["date"];
    $submittedPlace = $_POST["lieu"];
    $volunteersAssignedToCollectionArray = isset($_POST["benevoles"]) ? $_POST["benevoles"] : [];
    $wasteTypesArraySubmitted = isset($_POST['type_dechet']) ? $_POST['type_dechet'] : [];
    $quantitiesArraySubmitted = isset($_POST['quantite_kg']) ? $_POST['quantite_kg'] : [];

    try {
        $sqlQueryToInsertCollection = "INSERT INTO collectes (date_collecte, lieu) VALUES (?, ?)";
        $statementToAddCollection = $pdo->prepare($sqlQueryToInsertCollection);
        if (!$statementToAddCollection->execute([$submittedDate, $submittedPlace])) {
            die("Erreur lors de l'insertion de la date et du lieu de la collecte dans la base de données.");
        }
        $collectionId = $pdo->lastInsertId();

        $sqlQueryToInsertVolunteersCollectionAssignment = "INSERT INTO benevoles_collectes (id_collecte, id_benevole) VALUES (?, ?)";
        $statementToAddVolunteersCollectionAssignment = $pdo->prepare($sqlQueryToInsertVolunteersCollectionAssignment);
        foreach ($volunteersAssignedToCollectionArray as $volunteerId) {
            if (!$statementToAddVolunteersCollectionAssignment->execute([$collectionId, $volunteerId])) {
                die("Erreur lors de l'assignation des bénévoles à la collecte dans la base de données.");
            }
        }

        if (isset($wasteTypesArraySubmitted) && isset($quantitiesArraySubmitted)) {
            $sqlQueryToInsertCollectedWaste = "INSERT INTO dechets_collectes (id_collecte, type_dechet, quantite_kg) VALUES (?, ?, ?)";
            $statementToAddCollectedWaste = $pdo->prepare($sqlQueryToInsertCollectedWaste);
            for ($index = 0; $index < count($wasteTypesArraySubmitted); $index++) {
                if (!empty($wasteTypesArraySubmitted[$index]) && is_numeric($quantitiesArraySubmitted[$index]) && !$statementToAddCollectedWaste->execute([$collectionId, $wasteTypesArraySubmitted[$index], $quantitiesArraySubmitted[$index]])) {
                    die("Erreur lors de l'insertion des déchets collectés dans la base de données.");
                }
            }
        }
    } catch (PDOException $pdoException) {
        echo "Erreur de base de données : " . $pdoException->getMessage();
        exit;
    }

    header("Location: collection_list.php");
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
    <title>Ajouter une collecte</title>
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex h-screen">
        <?php require 'navbar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto">
            <h1 class="text-4xl font-bold text-cyan-950 mb-6">Ajouter une collecte</h1>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <form method="POST" class="space-y-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">
                            Date
                            <input type="date" id="date" name="date"
                                class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required />
                        </label>
                    </div>

                    <div>
                        <label for="lieu" class="block text-sm font-medium text-gray-700">
                            Lieu
                            <input type="text" id="lieu" name="lieu"
                                class="w-full mt-2 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Lieu de la collecte" required />
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bénévoles
                            <?php if (!empty($volunteersArray)): ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <?php foreach ($volunteersArray as $volunteer): ?>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="benevoles[]" value="<?= $volunteer['id'] ?>" id="benevole_<?= $volunteer['id'] ?>" class="mr-2" />
                                            <label for="benevole_<?= $volunteer['id'] ?>" class="text-gray-700">
                                                <?= $volunteer['nom'] ?>
                                            </label>
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
                            <div id="waste-container">
                                <div class="waste-item flex space-x-4 mb-2">
                                    <select name="type_dechet[]" class="w-full p-2 border border-gray-300 rounded-lg">
                                        <?php $selectOptionsArray = "<option value=''>Sélectionner un type</option>";
                                        foreach ($collectedWastesArray as $wasteType): ?>
                                            <?php $selectOptionsArray .= "<option value='" . htmlspecialchars($wasteType) . "'>" . htmlspecialchars($wasteType) . "</option>"; ?>
                                        <?php endforeach; ?>
                                        <?= $selectOptionsArray ?>
                                    </select>
                                    <input type="number" min="0" step="0.1" name="quantite_kg[]" placeholder="Quantité (kg)" class="w-full p-2 border border-gray-300 rounded-lg" />
                                    <button type="button" class="bg-cyan-950 remove-waste hover:bg-red-600 text-white px-2 py-1 rounded">
                                        Supprimer
                                    </button>
                                </div>
                            </div>
                            <button type="button" id="add-waste" class="bg-cyan-950 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mt-2">
                                Ajouter un déchet
                            </button>
                        </label>
                    </div>

                    <?php
                    $cancelUrl = "collection_list.php";
                    $cancelTitle = "Retour à la liste des collectes";
                    $buttonTitle = "Ajouter la collecte";
                    $buttonTextContent = "Ajouter la collecte";
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
                    <?= $selectOptionsArray ?>
                </select>
                <input type="number" min="0" step="0.1" name="quantite_kg[]" placeholder="Quantité (kg)" class="w-full p-2 border border-gray-300 rounded-lg" />
                <button type="button" class="bg-cyan-950 remove-waste hover:bg-red-600 text-white px-2 py-1 rounded">
                    Supprimer
                </button>
            </div>
        `;

        function updateWasteSelectOptions() {
            const selects = document.querySelectorAll("select[name='type_dechet[]']");
            let selectedValues = Array.from(selects)
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
    </script>
</body>

</html>