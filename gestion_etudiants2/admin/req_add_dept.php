<?php
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nom_departement = $_POST['nom_departement'];

        try {
            $sql = "INSERT INTO departement (nom_departement) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nom_departement]);
            header("Location: index.php?menu=departements&success=Département créé avec succès");
            exit;
        } catch (PDOException $e) {
            header("Location: index.php?menu=departements&error=" . urlencode("Erreur : " . $e->getMessage()));
            exit;
        }
    }
} else {
    header("Location: ../connexion.php");
    exit;
}