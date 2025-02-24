<?php
/**
 * Returns pagination parameters.
 *
 * @param int $defaultLimit
 * @return array [limit, pageNumber, offset]
 */
function getPaginationParams($defaultLimit = 3)
{
    $limit = $defaultLimit;
    $pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
    $offset = ($pageNumber - 1) * $limit;
    return compact('limit', 'pageNumber', 'offset');
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
}
