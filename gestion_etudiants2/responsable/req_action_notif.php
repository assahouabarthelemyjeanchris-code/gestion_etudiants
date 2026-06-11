<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Responsable') {
    header("Location: ../connexion.php");
    exit;
}

include_once "../DB_connexion.php";

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    if ($action === 'lu') {
        $stmt = $conn->prepare("UPDATE notifications SET statut_lecture = 1 WHERE id_notification = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'historique') {
        $stmt = $conn->prepare("UPDATE notifications SET statut_lecture = 2 WHERE id_notification = ?");
        $stmt->execute([$id]);
    }
}

header("Location: index.php");
exit;