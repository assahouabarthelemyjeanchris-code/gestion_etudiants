<?php
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        try {
            $sql = "DELETE FROM departement WHERE id_departement = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);
            header("Location: index.php?menu=departements&success=Département supprimé");
            exit;
        } catch (PDOException $e) {
            header("Location: index.php?menu=departements&error=" . urlencode("Impossible de supprimer : ce département contient des filières actives."));
            exit;
        }
    }
} else {
    header("Location: ../connexion.php");
    exit;
}