<?php 
session_start();
include "../DB_connexion.php";

if (isset($_SESSION['admin_id']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $admin_id = $_SESSION['admin_id'];
    
    // Récupération des données du formulaire
    $nom    = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email  = $_POST['email'];
    $uname  = $_POST['uname'];
    $old_p  = $_POST['old_pass'];
    $new_p  = $_POST['new_pass'];

    // 1. Vérifier si l'admin existe et récupérer son mot de passe actuel
    $sql = "SELECT * FROM administrateur WHERE administrateur_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$admin_id]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. Vérification du mot de passe actuel
        // On teste le hachage OU le texte clair (au cas où votre base n'est pas encore hachée)
        if (password_verify($old_p, $user['motsdepasse']) || $old_p == $user['motsdepasse']) {
            
            if (!empty($new_p)) {
                // Option A : On change aussi le mot de passe
                $new_p_hash = password_hash($new_p, PASSWORD_DEFAULT);
                $sql = "UPDATE administrateur SET nom=?, prenom=?, email=?, nomutilisateur=?, motsdepasse=? WHERE administrateur_id=?";
                $stmt = $conn->prepare($sql);
                $res = $stmt->execute([$nom, $prenom, $email, $uname, $new_p_hash, $admin_id]);
            } else {
                // Option B : On change tout sauf le mot de passe
                $sql = "UPDATE administrateur SET nom=?, prenom=?, email=?, nomutilisateur=? WHERE administrateur_id=?";
                $stmt = $conn->prepare($sql);
                $res = $stmt->execute([$nom, $prenom, $email, $uname, $admin_id]);
            }

            if ($res) {
                header("Location: index.php?success=Modification réussie !");
                exit;
            } else {
                header("Location: index.php?error=Erreur lors de la mise à jour SQL");
                exit;
            }
        } else {
            header("Location: index.php?error=L'ancien mot de passe est incorrect");
            exit;
        }
    } else {
        header("Location: index.php?error=Utilisateur introuvable");
        exit;
    }
} else {
    header("Location: ../connexion.php");
    exit;
}