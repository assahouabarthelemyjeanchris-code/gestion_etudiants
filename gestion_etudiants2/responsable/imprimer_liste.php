<?php
session_start();
require_once "../DB_connexion.php";

// 1. Vérification de l'accès (Responsable uniquement)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Responsable') {
    die("Accès refusé : Connexion requise.");
}

$id_responsable = $_SESSION['user_id'];

// Récupération de l'année universitaire active depuis la configuration globale
try {
    $annee_active = $conn->query("SELECT valeur FROM configuration_gde WHERE cle = 'annee_universitaire_active'")->fetchColumn();
    if (!$annee_active) {
        $annee_active = date('Y') . "-" . (date('Y') + 1);
    }
} catch (Exception $e) {
    $annee_active = date('Y') . "-" . (date('Y') + 1);
}

// 2. Récupération des filtres depuis l'URL (GET)
$filtre_filiere = isset($_GET['filiere']) ? intval($_GET['filiere']) : 0;
$filtre_niveau  = isset($_GET['niveau']) ? trim($_GET['niveau']) : '';
// Si aucune année n'est transmise ou si elle est vide, on prend l'année active par défaut
$filtre_annee   = !empty($_GET['annee']) ? trim($_GET['annee']) : $annee_active;

try {
    // 3. Construction de la requête SQL basée sur le département du responsable (Multi-filières)
    $sql = "SELECT e.id_etudiants, e.nom, e.prenom, e.niveau, f.nom_filiere, i.annee_academique 
            FROM etudiants e
            JOIN filiere f ON e.id_filiere = f.id_filiere
            JOIN departement d ON f.id_departement = d.id_departement
            LEFT JOIN inscription i ON e.id_etudiants = i.id_etudiants
            WHERE d.id_responsable = ? AND e.statut_inscription = 'Validé définitivement'";

    $params = [$id_responsable];

    // Application des clauses de filtres si elles sont définies
    if ($filtre_filiere > 0) {
        $sql .= " AND e.id_filiere = ?";
        $params[] = $filtre_filiere;
    }
    if (!empty($filtre_niveau)) {
        $sql .= " AND e.niveau = ?";
        $params[] = $filtre_niveau;
    }
    if (!empty($filtre_annee)) {
        $sql .= " AND i.annee_academique = ?";
        $params[] = $filtre_annee;
    }

    $sql .= " ORDER BY f.nom_filiere ASC, e.niveau ASC, e.nom ASC, e.prenom ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $liste = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Détermination du libellé de la filière pour l'en-tête de la grille
    if ($filtre_filiere > 0 && count($liste) > 0) {
        $filiere_affichage = $liste[0]['nom_filiere'];
    } elseif ($filtre_filiere > 0) {
        // Si la liste est vide mais qu'une filière spécifique est demandée, on récupère son nom
        $stmt_f_name = $conn->prepare("SELECT nom_filiere FROM filiere WHERE id_filiere = ?");
        $stmt_f_name->execute([$filtre_filiere]);
        $f_res = $stmt_f_name->fetch(PDO::FETCH_ASSOC);
        $filiere_affichage = $f_res['nom_filiere'] ?? "Filière Spécifique";
    } else {
        $filiere_affichage = "Toutes les Filières du Département";
    }

} catch (Exception $e) {
    die("Erreur SQL : " . $e->getMessage());
}

$total = count($liste);

// Détermination de l'année universitaire à afficher dans l'en-tête
if (!empty($filtre_annee)) {
    $annee_affichage = $filtre_annee;
} else {
    $annee_affichage = ($total > 0 && !empty($liste[0]['annee_academique'])) ? $liste[0]['annee_academique'] : date('Y') . '/' . (date('Y') + 1);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Grille de Notes Officielle - <?= htmlspecialchars($filiere_affichage) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root { 
            --fond: #fafefc;
            --fond-secondaire: #fbf4f0;
            --blanc: #fffffe;
            --texte: #090b3c;
            --texte-secondaire: #573e4d;
            --primaire: #a21c3b;
            --primaire-hover: #391f20;
            --bordure: #000000; /* Forcé en noir pur pour une impression de grille parfaite */
            --gris-impression: #f2f2f2;
        }

        @page { 
            size: A4 landscape; /* Format paysage pour une grille claire et aérée */
            margin: 10mm; 
        }
        
        body { 
            font-family: 'Arial', 'Helvetica', sans-serif; 
            margin: 0; 
            padding: 20px; 
            background-color: var(--blanc);
            color: var(--texte);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        .page-container { 
            width: 100%; 
            margin: 0 auto;
        }

        /* Barre d'action utilisateur */
        .no-print-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            background: var(--fond-secondaire);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #d9d6df;
        }
        .btn-action { 
            padding: 10px 20px; 
            font-size: 10pt;
            font-weight: bold;
            border: none; 
            border-radius: 8px; 
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-print { background: var(--primaire); color: white; }
        .btn-print:hover { background: var(--primaire-hover); }
        .btn-back { background: #7d6f77; color: white; }
        .btn-back:hover { background: #573e4d; }

        /* Structure Grille Académique Excel */
        .excel-table { 
            width: 100%; 
            border-collapse: collapse; 
            border: 2px solid var(--bordure);
            table-layout: fixed; 
        }
        
        .excel-table td { 
            border: 1px solid var(--bordure); 
            height: 32px; /* Rehaussé pour l'écriture manuscrite des notes */
            padding: 4px 8px; 
            font-size: 9pt;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        /* En-têtes Universitaires Institutionnels */
        .dept-header { 
            background-color: var(--primaire) !important; 
            color: white !important;
            font-weight: bold; 
            text-align: center; 
            font-size: 11pt; 
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 2px solid var(--bordure) !important; 
        }
        
        .level-header { 
            color: var(--primaire); 
            font-weight: 800; 
            text-align: center; 
            font-size: 14pt; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            border: none !important; 
        }

        .meta-text {
            font-size: 10pt;
            font-weight: bold;
            color: var(--texte-secondaire);
        }

        /* Configuration des largeurs de colonnes */
        .w-num { width: 45px; text-align: center; }
        .w-nom { width: 200px; }
        .w-prenom { width: 260px; } 
        .w-filiere { width: 140px; }
        .w-note { width: 75px; text-align: center; } 

        .table-th {
            text-align: center; 
            font-weight: bold; 
            background-color: var(--gris-impression) !important;
            font-size: 9.5pt;
            text-transform: uppercase;
        }
        
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-upper { text-transform: uppercase; }

        /* Pied de page de la liste */
        .list-footer {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            font-size: 10pt;
            font-weight: bold;
            color: var(--texte-secondaire);
        }

        @media print { 
            .no-print-bar { display: none; } 
            body { padding: 0; background-color: #fff; }
            .excel-table { border: 2px solid #000; }
            .excel-table td { border: 1px solid #000; }
            .dept-header { background-color: #a21c3b !important; color: #fff !important; }
            .table-th { background-color: #e2e2e2 !important; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button class="btn-action btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Lancer l'impression
        </button>
        <a href="classes.php" class="btn-action btn-back">
            <i class="fa-solid fa-arrow-left"></i> Retour à la gestion des étudiants
        </a>
    </div>

    <div class="page-container">
        <table class="excel-table">
            <tr>
                <td colspan="1" style="border:none"></td>
                <td colspan="4" class="dept-header"><?= htmlspecialchars($filiere_affichage) ?></td>
                <td colspan="2" style="border:none"></td>
            </tr>
            
            <tr><td colspan="7" style="border:none; height:12px;"></td></tr>

            <tr>
                <td colspan="3" style="border:none; text-align: left;" class="meta-text">
                    <i class="fa-solid fa-calendar-days"></i> Année Universitaire : <?= htmlspecialchars($annee_affichage) ?>
                </td>
                <td colspan="4" style="border:none; text-align: right;" class="meta-text">
                    Matière / ECUE : ___________________________
                </td>
            </tr>

            <tr>
                <td colspan="7" class="level-header">
                    LISTE DE CLASSE : <?= !empty($filtre_niveau) ? htmlspecialchars($filtre_niveau) : 'Tous Niveaux Confondus' ?>
                </td>
            </tr>

            <tr style="text-align: center;">
                <td class="w-num table-th">N°</td>
                <td class="w-nom table-th">Nom</td>
                <td class="w-prenom table-th">Prénom</td>
                <td class="w-filiere table-th">Filière / Niv.</td>
                <td class="w-note table-th">Note CC</td>
                <td class="w-note table-th">Note DS</td>
                <td class="w-note table-th">Moyenne</td>
            </tr>

            <?php 
            if ($total > 0):
                foreach ($liste as $key => $row): 
            ?>
            <tr>
                <td class="text-center fw-bold"><?= $key + 1 ?></td>
                <td class="text-upper fw-bold"><?= htmlspecialchars($row['nom']) ?></td>
                <td style="text-transform: capitalize; white-space: normal; word-break: break-word; font-weight: 500;"><?= htmlspecialchars($row['prenom']) ?></td>
                <td class="text-center"><small><?= htmlspecialchars($row['nom_filiere']) ?> (<?= htmlspecialchars($row['niveau']) ?>)</small></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php 
                endforeach; 
            else:
                echo "<tr><td colspan='7' class='text-center fw-bold' style='height:40px; color:var(--texte-secondaire);'>Aucun étudiant inscrit trouvé pour ces critères.</td></tr>";
            endif; 
            ?>

            <?php for($j=0; $j<5; $j++): ?>
            <tr>
                <td class="text-center fw-bold" style="background-color: var(--gris-impression) !important;"><?= $total + $j + 1 ?></td>
                <?php for($i=0; $i<6; $i++) echo '<td></td>'; ?>
            </tr>
            <?php endfor; ?>
        </table>

        <div class="list-footer">
            <span>Effectif validé évalué : <?= $total ?> étudiant(s)</span>
            <span style="border-top: 1px solid #000; width: 280px; text-align: center; padding-top: 5px; margin-top: 15px;">Signature du Responsable / Enseignant</span>
        </div>
    </div>

</body>
</html>