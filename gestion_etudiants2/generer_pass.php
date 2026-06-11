<?php
// Le mot de passe que vous voulez utiliser pour vous connecter
$password_en_clair = "chris123"; 

// Génération du hash sécurisé
$hash = password_hash($password_en_clair, PASSWORD_DEFAULT);

echo "Votre mot de passe en clair : <b>" . $password_en_clair . "</b><br>";
echo "Le hash à copier dans votre base SQL : <br><input style='width:400px' value='" . $hash . "' readonly>";
?>