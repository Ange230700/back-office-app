<?php

/**
 * Returns pagination parameters.
 *
 * @param int $defaultLimit
 * @return array [limit, pageNumber, offset]
 */
function getPaginationParams($defaultLimit = 3)
{
    try {
        $limit = $defaultLimit;
        $pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
        $offset = ($pageNumber - 1) * $limit;
        return compact('limit', 'pageNumber', 'offset');
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

/**
 * Executes a paginated query and its count query.
 *
 * @param PDO    $pdo       The PDO connection.
 * @param string $sql       The main SQL query with :limit and :offset placeholders.
 * @param string $countSql  The SQL to count total records.
 * @param array  $params    (Optional) Additional parameters for the main query.
 *
 * @return array [$results, $totalPages]
 */
function runPaginatedQuery(PDO $pdo, $sql, $countSql, $params = [])
{
    try {
        $pagination = getPaginationParams();
        $params[':limit'] = $pagination['limit'];
        $params[':offset'] = $pagination['offset'];

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        if (!$stmt->execute()) {
            die("Erreur lors de l'exécution de la requête SQL.");
        }
        $results = $stmt->fetchAll();

        $stmtCount = $pdo->prepare($countSql);
        if (!$stmtCount->execute()) {
            die("Erreur lors de l'exécution de la requête SQL (count).");
        }
        $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPages = ceil($total / $pagination['limit']);

        return [$results, $totalPages];
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

// Additional queries specific to collections (statistics)
function getCollectedWastesTotalQuantity(PDO $pdo)
{
    try {
        $sqlQueryToSelectCollectedWastesTotalQuantity = "SELECT COALESCE(ROUND(SUM(COALESCE(dechets_collectes.quantite_kg,0)),1), 0)
            AS quantite_total_des_dechets_collectes
            FROM collectes
            LEFT JOIN dechets_collectes ON collectes.id = dechets_collectes.id_collecte";
        $statementToGetCollectedWastesTotalQuantity = $pdo->prepare($sqlQueryToSelectCollectedWastesTotalQuantity);
        if (!$statementToGetCollectedWastesTotalQuantity->execute()) {
            die("Erreur lors de l'exécution de la requête SQL.");
        }
        return $statementToGetCollectedWastesTotalQuantity->fetch(PDO::FETCH_ASSOC)['quantite_total_des_dechets_collectes'];
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

function getMostRecentCollection(PDO $pdo)
{
    try {
        $sqlQueryToSelectMostRecentCollection = "SELECT lieu, date_collecte
            FROM collectes
            WHERE date_collecte <= CURDATE()
            ORDER BY date_collecte DESC
            LIMIT 1";
        $statementToGetMostRecentCollection = $pdo->prepare($sqlQueryToSelectMostRecentCollection);
        if (!$statementToGetMostRecentCollection->execute()) {
            die("Erreur lors de l'exécution de la requête SQL.");
        }
        return $statementToGetMostRecentCollection->fetch();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

function getNextCollection(PDO $pdo)
{
    try {
        $sqlQueryToSelectNextCollection = "SELECT lieu, date_collecte
            FROM collectes
            WHERE date_collecte > CURDATE()
            ORDER BY date_collecte ASC
            LIMIT 1";
        $statementToGetNextCollection = $pdo->prepare($sqlQueryToSelectNextCollection);
        if (!$statementToGetNextCollection->execute()) {
            die("Erreur lors de l'exécution de la requête SQL.");
        }
        return $statementToGetNextCollection->fetch();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}
