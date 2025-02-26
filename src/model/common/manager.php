<?php

/* ------------------- CREATE ------------------- */
/* ============================================== */

/* ------------------- READ ------------------- */
function getList(PDO $pdo, $sqlQueryToSelectList, $params)
{
    try {
        $statementToGetList = $pdo->prepare($sqlQueryToSelectList);
        foreach ($params as $key => $value) {
            $statementToGetList->bindValue(
                $key,
                $value,
                is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
        if (!$statementToGetList->execute()) {
            die("Erreur lors de l'exécution de la requête SQL.");
        }
        return $statementToGetList->fetchAll();
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

function getListLength(PDO $pdo, $sqlQueryToSelectListLength)
{
    try {
        $statementToGetListLength = $pdo->prepare($sqlQueryToSelectListLength);
        if (!$statementToGetListLength->execute()) {
            die("Erreur lors de l'exécution de la requête SQL (count).");
        }
        return $statementToGetListLength->fetch(PDO::FETCH_ASSOC)['total'];
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

function runPaginatedQuery(PDO $pdo, $sqlQueryToSelectList, $sqlQueryToSelectListLength, $params = [])
{
    $paginationParams = getPaginationParams();
    $params[':limit'] = $paginationParams['limit'];
    $params[':offset'] = $paginationParams['offset'];

    $result = getList($pdo, $sqlQueryToSelectList, $params);
    $listLength = getListLength($pdo, $sqlQueryToSelectListLength);
    $numberOfPages = ceil($listLength / $paginationParams['limit']);

    return [$result, $numberOfPages];
}
/* ============================================== */

/* ------------------- UPDATE ------------------- */
/* ============================================== */

/* ------------------- DELETE ------------------- */
/* ============================================== */