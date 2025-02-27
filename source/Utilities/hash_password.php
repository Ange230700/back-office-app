<?php
$password = "adalovelace";
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Mot de passe hashé : " . $hash;
