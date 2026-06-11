<?php
session_start();

// Vérification de la session admin
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Récupération des données du formulaire
        $forced_id      = $_POST['forced_id']; 
        $uname          = $_POST['uname'];
        $pass           = password_hash($_POST['pass'], PASSWORD_DEFAULT);
        $nom            = $_POST['nom'];
        $prenom         = $_POST['prenom'];
        $email          = $_POST['email'];
        $tel            = $_POST['telephone'];
        $id_departement = $_POST['id_departement']; // Récupération du département choisi

        try {
            $conn->beginTransaction();

            // 1. Insertion du responsable dans la nouvelle table
            $sql = "INSERT INTO responsable_departement (id_responsable, nomutilisateur, motdepasse, nom, prenom, email, telephone) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$forced_id, $uname, $pass, $nom, $prenom, $email, $tel]);

            // 2. Libérer l'ancien responsable de ce département s'il y en avait un
            $sql_clear = "UPDATE departement SET id_responsable = NULL WHERE id_departement = ?";
            $stmt_clear = $conn->prepare($sql_clear);
            $stmt_clear->execute([$id_departement]);

            // 3. Assigner le nouveau responsable au département sélectionné
            $sql_dept = "UPDATE departement SET id_responsable = ? WHERE id_departement = ?";
            $stmt_dept = $conn->prepare($sql_dept);
            $stmt_dept->execute([$forced_id, $id_departement]);

            $conn->commit();

            header("Location: index.php?menu=responsables&success=Le responsable et son département ont été configurés.");
            exit;

        } catch (PDOException $e) {
            $conn->rollBack();
            $error_msg = "Erreur : " . $e->getMessage();
            header("Location: index.php?menu=responsables&error=" . urlencode($error_msg));
            exit;
        }
    }
} else {
    header("Location: ../connexion.php");
    exit;
}