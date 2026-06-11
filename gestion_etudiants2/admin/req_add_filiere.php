<?php
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom            = $_POST['nom_filiere'];
        $abr            = $_POST['abreviation'];
        $niv            = $_POST['niveau'];
        // Si aucun département n'est choisi, on attribue la valeur NULL
        $id_departement = !empty($_POST['id_departement']) ? $_POST['id_departement'] : null;

        try {
            $sql = "INSERT INTO filiere (nom_filiere, abreviation_filiere, niveau, id_departement) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nom, $abr, $niv, $id_departement]);
            header("Location: index.php?menu=filieres&success=Filière créée et associée au département !");
            exit;
        } catch (PDOException $e) {
            header("Location: index.php?menu=filieres&error=" . urlencode("Erreur : " . $e->getMessage()));
            exit;
        }
    }
} else {
    header("Location: ../connexion.php");
    exit;
}