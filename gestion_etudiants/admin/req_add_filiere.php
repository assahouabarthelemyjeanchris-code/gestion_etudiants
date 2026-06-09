<?php
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom = $_POST['nom_filiere'];
        $abr = $_POST['abreviation'];
        $niv = $_POST['niveau'];

        try {
            $sql = "INSERT INTO filiere (nom_filiere, abreviation_filiere, niveau) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nom, $abr, $niv]);
            header("Location: index.php?menu=filieres&success=Filière créée");
        } catch (PDOException $e) {
            header("Location: index.php?menu=filieres&error=Erreur");
        }
    }
}