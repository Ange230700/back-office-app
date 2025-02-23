<?php
require 'init.php';

// Pagination parameters
$limitOfItemsOnOnePage = 3;
$pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
$offset = ($pageNumber - 1) * $limitOfItemsOnOnePage;

// Retrieve volunteers with their participations
$sql = "SELECT benevoles.id, benevoles.nom, benevoles.email, benevoles.role, COALESCE(GROUP_CONCAT(CONCAT(collectes.lieu, ' (', collectes.date_collecte, ')') SEPARATOR ', '), 'Aucune participation pour le moment') AS participations
    FROM benevoles
    LEFT JOIN benevoles_collectes ON benevoles.id = benevoles_collectes.id_benevole
    LEFT JOIN collectes ON collectes.id = benevoles_collectes.id_collecte
    GROUP BY benevoles.id
    ORDER BY benevoles.nom ASC
    LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':limit', $limitOfItemsOnOnePage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$volunteersList = $stmt->fetchAll();

// Get total number of volunteers for pagination
$countSql = "SELECT COUNT(*) AS total FROM benevoles";
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute();
$totalVolunteers = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalVolunteers / $limitOfItemsOnOnePage);

$pageTitle = "Liste des Bénévoles";
$pageHeader = "Liste des Bénévoles";

// Build the page content using output buffering
ob_start();
?>
<div class="overflow-hidden rounded-lg shadow-lg bg-white">
    <table class="w-full table-auto border-collapse">
        <thead class="bg-cyan-950 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Nom</th>
                <th class="py-3 px-4 text-left">Email</th>
                <th class="py-3 px-4 text-left">Rôle</th>
                <th class="py-3 px-4 text-left">Participations</th>
                <?php if ($_SESSION["role"] === "admin"): ?>
                    <th class="py-3 px-4 text-left">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-300">
            <?php foreach ($volunteersList as $volunteer): ?>
                <tr class="hover:bg-gray-100 transition duration-200">
                    <td class="py-3 px-4"><?= htmlspecialchars($volunteer["nom"]) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($volunteer["email"]) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($volunteer["role"]) ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars($volunteer["participations"]) ?></td>
                    <?php if ($_SESSION["role"] === "admin"):
                        // Set up URLs and titles for the action buttons
                        $editUrl = "volunteer_edit.php?id=" . $volunteer["id"];
                        $deleteUrl = "volunteer_delete.php?id=" . $volunteer["id"];
                        $editTitle = "Modifier " . htmlspecialchars($volunteer["nom"]);
                        $deleteTitle = "Supprimer " . htmlspecialchars($volunteer["nom"]);
                        require 'actionsButtons.php';
                    endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require 'paginationButtons.php'; ?>
<?php
$content = ob_get_clean();
require 'layout.php';
?>