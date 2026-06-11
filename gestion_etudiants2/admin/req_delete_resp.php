<?php
session_start();
if (isset($_SESSION['admin_id']) && $_SESSION['role'] == 'Admin') {
    include_once "../DB_connexion.php";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Suppression directe, la clé étrangère gère le reste (id_responsable passe à NULL dans departement)
        $sql = "DELETE FROM responsable_departement WHERE id_responsable = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        
        header("Location: index.php?menu=responsables&success=Le responsable a été supprimé avec succès.");
        exit;
    }
} else {
    header("Location: ../connexion.php");
    exit;
}