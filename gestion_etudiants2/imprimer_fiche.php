<?php 
include "DB_connexion.php"; 

if(!isset($_GET['id'])) {
    header("Location: inscription_view.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT e.*, f.nom_filiere FROM etudiants e 
                        JOIN filiere f ON e.id_filiere = f.id_filiere 
                        WHERE e.id_etudiants = ?");
$stmt->execute([$id]);
$etudiant = $stmt->fetch();

if(!$etudiant) die("Étudiant introuvable.");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche d'inscription - <?=$etudiant['nom']?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <style>
        /* Style pour l'écran */
        body { background: #f0f2f5; padding: 30px; }
        .fiche-print { background: white; padding: 40px; border: 1px solid #ddd; border-radius: 5px; max-width: 800px; margin: auto; position: relative; }
        .btn-print { margin-bottom: 20px; text-align: center; }
        .logo-placeholder { width: 80px; height: 80px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        
        /* Style spécifique pour l'IMPRESSION */
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .fiche-print { border: none; width: 100%; max-width: 100%; margin: 0; padding: 0; }
            @page { margin: 2cm; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="btn-print no-print">
            <button onclick="window.print()" class="btn btn-success btn-lg">
                 Imprimer ma fiche d'inscription
            </button>
            <a href="index.php" class="btn btn-outline-primary btn-lg">Retour à l'accueil</a>
        </div>

        <div class="fiche-print shadow">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">UNIVERSITÉ GDE</h2>
                    <p class="text-muted">Fiche d'inscription officielle</p>
                </div>
                <div class="logo-placeholder">GDE</div>
            </div>

            <hr>

            <div class="alert alert-secondary py-1 fw-bold">1. IDENTITÉ DE L'ÉTUDIANT</div>
            <table class="table table-sm table-borderless">
                <tr><th width="30%">ID Permanent :</th><td><strong><?=$etudiant['id_permanent']?></strong></td></tr>
                <tr><th>Nom & Prénoms :</th><td><?=$etudiant['nom'] . ' ' . $etudiant['prenom']?></td></tr>
                <tr><th>Sexe / Né(e) le :</th><td><?=$etudiant['sexe']?> , le <?=date('d/m/Y', strtotime($etudiant['date_naissance']))?> à <?=$etudiant['lieu_naissance']?></td></tr>
                <tr><th>Nationalité :</th><td><?=$etudiant['nationalite']?></td></tr>
            </table>

            <div class="alert alert-secondary py-1 fw-bold">2. CURSUS ACADÉMIQUE</div>
            <table class="table table-sm table-borderless">
                <tr><th width="30%">Filière :</th><td><?=$etudiant['nom_filiere']?></td></tr>
                <tr><th>Niveau / Classe :</th><td><?=$etudiant['niveau']?> (<?=$etudiant['niveau_etude']?>)</td></tr>
                <tr><th>Régime :</th><td><?=$etudiant['cours']?></td></tr>
                <tr><th>Date Inscription :</th><td><?=date('d/m/Y H:i', strtotime($etudiant['date_inscription']))?></td></tr>
            </table>

            <div class="alert alert-secondary py-1 fw-bold">3. CONTACTS & SANTÉ</div>
            <table class="table table-sm table-borderless">
                <tr><th width="30%">Email :</th><td><?=$etudiant['email']?></td></tr>
                <tr><th>Téléphone :</th><td><?=$etudiant['contact_etudiant']?></td></tr>
                <tr><th>Adresse :</th><td><?=$etudiant['adresse']?></td></tr>
                <tr><th>Infos Santé :</th><td>
                    Asthme: <?=$etudiant['asthme']?> | Tension: <?=$etudiant['hypertension']?> | Handicap: <?=$etudiant['handicap_physique']?>
                </td></tr>
            </table>

            <div class="alert alert-secondary py-1 fw-bold">4. RESPONSABLE LÉGAL</div>
            <table class="table table-sm table-borderless">
                <tr><th width="30%">Tuteur :</th><td><?=$etudiant['tuteur_legal']?></td></tr>
                <tr><th>Téléphone Tuteur :</th><td><?=$etudiant['telephone_tuteur']?></td></tr>
            </table>

            <div class="mt-5 d-flex justify-content-between">
                <div class="text-center" style="width: 200px; border-top: 1px solid #000; padding-top: 10px;">
                    Signature Étudiant
                </div>
                <div class="text-center" style="width: 200px; border-top: 1px solid #000; padding-top: 10px;">
                    Cachet Administration
                </div>
            </div>
        </div>
    </div>

</body>
</html>