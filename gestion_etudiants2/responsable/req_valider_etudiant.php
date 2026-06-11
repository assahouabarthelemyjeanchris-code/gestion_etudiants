<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Responsable') {
    header("Location: ../connexion.php");
    exit;
}

include_once "../DB_connexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_notification = intval($_POST['id_notification']);
    $id_etudiants = intval($_POST['id_etudiants']);
    $prenom_resp = $_SESSION['prenom'] ?? 'Responsable';
    $nom_resp = $_SESSION['nom'] ?? '';

    try {
        $conn->beginTransaction();

        // 1. Mettre à jour le statut d'inscription de l'étudiant
        $stmt_etud = $conn->prepare("UPDATE etudiants SET statut_inscription = 'Validé définitivement' WHERE id_etudiants = ?");
        $stmt_etud->execute([$id_etudiants]);

        // Récupérer le nom complet de l'étudiant pour personnaliser le message destiné à l'admin
        $stmt_info = $conn->prepare("SELECT nom, prenom FROM etudiants WHERE id_etudiants = ?");
        $stmt_info->execute([$id_etudiants]);
        $etudiant = $stmt_info->fetch(PDO::FETCH_ASSOC);
        
        $nom_complet_etudiant = $etudiant ? strtoupper($etudiant['nom'])." ".$etudiant['prenom'] : "Un étudiant";

        // 2. Mettre la notification actuelle du Responsable en archive / historique (statut_lecture = 2)
        $stmt_notif_resp = $conn->prepare("UPDATE notifications SET statut_lecture = 2 WHERE id_notification = ?");
        $stmt_notif_resp->execute([$id_notification]);

        // 3. Envoyer une notification d'inscription validée à l'administrateur
        $message_admin = "Le responsable " . $prenom_resp . " " . $nom_resp . " a validé définitivement l'inscription de l'étudiant : " . $nom_complet_etudiant . " (ID: " . $id_etudiants . ").";
        
        $stmt_admin = $conn->prepare("INSERT INTO notifications (destinataire_role, id_destinataire_specifique, message, statut_lecture, created_at) VALUES ('Admin', NULL, ?, 0, NOW())");
        $stmt_admin->execute([$message_admin]);

        $conn->commit();
        header("Location: index.php?success=L'étudiant a été validé avec succès ! L'administrateur a été notifié.");
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: index.php?error=Erreur lors de la validation : " . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}