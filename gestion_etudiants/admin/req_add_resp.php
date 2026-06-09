<?php
session_start();

// Vérification de la session admin
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Récupération des données du formulaire
        $forced_id = $_POST['forced_id']; // L'ID manuel
        $uname    = $_POST['uname'];
        $pass     = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        $nom      = $_POST['nom'];
        $prenom   = $_POST['prenom'];
        $email    = $_POST['email'];
        $tel      = $_POST['telephone'];
        $filiere  = $_POST['filiere'];

        try {
            // Requête incluant explicitement la colonne id_responsablefiliere
            $sql = "INSERT INTO responsablefiliere (id_responsablefiliere, nomutilisateur, motdepasse, nom, prenom, email, telephone, filiere) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$forced_id, $uname, $pass, $nom, $prenom, $email, $tel, $filiere]);

            // Redirection avec succès
            header("Location: index.php?menu=responsables&success=Le responsable ID: $forced_id a été créé.");
            exit;

        } catch (PDOException $e) {
            // Gestion d'erreur (ex: si l'ID existe déjà)
            $error_msg = "Erreur : L'ID ou le nom d'utilisateur est déjà utilisé.";
            header("Location: index.php?menu=responsables&error=" . urlencode($error_msg));
            exit;
        }
    }
} else {
    header("Location: ../connexion.php");
    exit;
}