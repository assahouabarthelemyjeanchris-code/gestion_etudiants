<?php
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "DELETE FROM filiere WHERE id_filiere = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        header("Location: index.php?menu=filieres&success=Supprimé");
    }
}