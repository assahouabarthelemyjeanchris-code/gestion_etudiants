<?php 
session_start();
include "../DB_connexion.php";

if (isset($_POST['uname'], $_POST['pass'], $_POST['role'])) {

    $uname = htmlspecialchars($_POST['uname']);
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    if (empty($uname) || empty($pass)) {
        header("Location: ../connexion.php?error=Veuillez remplir tous les champs");
        exit;
    }

    // Configuration dynamique selon le rôle choisi
    if ($role == '1') {
        // Cas Administrateur
        $sql = "SELECT * FROM administrateur WHERE nomutilisateur = ?";
        $session_role = "Admin";
        $redirect = "../admin/index.php";
    } else {
        // Cas Responsable Filière (Nom de table exact : responsablefiliere)
        $sql = "SELECT * FROM responsablefiliere WHERE nomutilisateur = ?";
        $session_role = "Responsable";
        $redirect = "../responsable/index.php";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute([$uname]);

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        
        // Gestion de la différence de nom de colonne pour le mot de passe
        // 'motsdepasse' pour admin, 'motdepasse' pour responsable
        $db_pass = ($role == '1') ? $user['motsdepasse'] : $user['motdepasse'];

        if (password_verify($pass, $db_pass)) {
            
            // On régénère l'ID pour la sécurité
            session_regenerate_id(true);

            // Création des variables de session communes
            $_SESSION['role'] = $session_role;
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['welcome'] = true;

            // Variables de session spécifiques
            if ($role == '1') {
                $_SESSION['admin_id'] = $user['administrateur_id'];
                $_SESSION['user_id'] = $user['administrateur_id'];
            } else {
                $_SESSION['user_id'] = $user['id_responsablefiliere'];
                $_SESSION['filiere'] = $user['filiere']; // Important pour l'affichage responsable
            }

            header("Location: $redirect");
            exit;
        } else {
            header("Location: ../connexion.php?error=Mot de passe incorrect");
            exit;
        }
    } else {
        header("Location: ../connexion.php?error=Utilisateur introuvable");
        exit;
    }
} else {
    header("Location: ../connexion.php");
    exit;
}