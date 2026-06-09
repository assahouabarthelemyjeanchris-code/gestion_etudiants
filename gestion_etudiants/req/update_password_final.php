<?php
include_once "../DB_connexion.php";

if (isset($_POST['new_pass']) && isset($_POST['token'])) {
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $token = $_POST['token'];

    if ($new_pass !== $confirm_pass) {
        header("Location: ../reset_password.php?token=$token&error=Les mots de passe ne correspondent pas");
        exit;
    }

    // Hachage sécurisé du mot de passe
    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

    // Configuration précise des tables et de leurs colonnes respectives
    $tables_config = [
        'administrateur' => 'motsdepasse', // avec 's'
        'responsablefiliere' => 'motdepasse' // sans 's'
    ];

    $updated = false;

    foreach ($tables_config as $table => $password_column) {
        // 1. On vérifie si le token appartient à cette table
        $check = $conn->prepare("SELECT * FROM $table WHERE reset_token = ?");
        $check->execute([$token]);
        
        if ($check->rowCount() > 0) {
            // 2. On met à jour la BONNE colonne dans la BONNE table
            $sql = "UPDATE $table SET $password_column = ?, reset_token = NULL WHERE reset_token = ?";
            $stmt = $conn->prepare($sql);
            $res = $stmt->execute([$hashed_password, $token]);

            if ($res) {
                $updated = true;
                break; // On arrête la boucle dès que c'est mis à jour
            }
        }
    }

    if ($updated) {
        header("Location: ../connexion.php?success=Votre mot de passe a été mis à jour avec succès !");
        exit;
    } else {
        // Si aucun utilisateur n'a été trouvé avec ce token
        header("Location: ../forgot_password.php?error=Lien invalide ou expiré.");
        exit;
    }
} else {
    header("Location: ../connexion.php");
    exit;
}