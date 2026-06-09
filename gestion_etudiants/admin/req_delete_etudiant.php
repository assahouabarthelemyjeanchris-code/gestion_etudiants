<?php 
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "DELETE FROM etudiants WHERE id_etudiants=?";
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute([$id]);

        header("Location: index.php?menu=etudiants");
        exit;
    }
} else {
    header("Location: ../connexion.php");
    exit;
}