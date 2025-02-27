<?php

/* ------------------- CREATE ------------------- */
/* ============================================== */

/* ------------------- READ ------------------- */
function readList(PDO $pdo, $sqlQueryToSelectList, $params)
{
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
}

function readListLength(PDO $pdo, $sqlQueryToSelectListLength)
{
    $statementToGetListLength = $pdo->prepare($sqlQueryToSelectListLength);
    if (!$statementToGetListLength->execute()) {
        die("Erreur lors de l'exécution de la requête SQL (count).");
    }
    return $statementToGetListLength->fetch(PDO::FETCH_ASSOC)['total'];
}
/* ============================================== */

/* ------------------- UPDATE ------------------- */
/* ============================================== */

/* ------------------- DELETE ------------------- */
/* ============================================== */