<!-- ca démarre la session, ca détruit les infos de la session puis la session et redirige vers login.php -->
<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit;
