<?php
session_start();

function checkUserLoggedIn()
{
    if (!isset($_SESSION["user_id"])) {
        header('Location: login.php');
        exit();
    }
}

function checkUserAdmin()
{
    checkUserLoggedIn();

    if ($_SESSION["role"] !== "admin") {
        header("Location: index.php");
        exit();
    }
}

function getPaginationParams($defaultLimit = 3)
{
    $limit = $defaultLimit;
    $pageNumber = isset($_GET["pageNumber"]) ? (int)$_GET["pageNumber"] : 1;
    $offset = ($pageNumber - 1) * $limit;
    return compact('limit', 'pageNumber', 'offset');
}