<?php
require_once BASE_PATH . '/src/models/common/manager.php';

/* ------------------- ADD ------------------- */
/* ============================================== */

/* ------------------- GET ------------------- */
function getList(PDO $pdo, $sqlQueryToSelectList, $params)
{
    try {
        return readList($pdo, $sqlQueryToSelectList, $params);
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
    }
}

function getListLength(PDO $pdo, $sqlQueryToSelectListLength)
{
    try {
        return readListLength($pdo, $sqlQueryToSelectListLength);
    } catch (PDOException $e) {
        echo "Erreur de base de données : " . $e->getMessage();
        exit;
    }
}

function runPaginatedQuery(PDO $pdo, $sqlQueryToSelectList, $sqlQueryToSelectListLength, $params =  [])
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

/* ------------------- EDIT ------------------- */
/* ============================================== */

/* ------------------- ERASE ------------------- */
/* ============================================== */
