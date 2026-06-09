<?php
session_start();
require_once "../DB_connexion.php";

$niveau = $_GET['niveau'] ?? null;
$filiere = $_SESSION['filiere'] ?? null;

if (!$niveau || !$filiere) die("Accès refusé : Paramètres manquants.");

try {
    $stmt = $conn->prepare("SELECT e.id_etudiants, e.nom, e.prenom, i.annee_academique 
                            FROM etudiants e
                            JOIN inscription i ON e.id_etudiants = i.id_etudiants
                            JOIN filiere f ON e.id_filiere = f.id_filiere
                            WHERE f.nom_filiere = ? AND e.niveau = ? 
                            ORDER BY e.nom ASC");
    $stmt->execute([$filiere, $niveau]);
    $liste = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erreur SQL : " . $e->getMessage());
}

$total = count($liste);
$annee_affichage = ($total > 0) ? $liste[0]['annee_academique'] : "2023/2024";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Grille de Notes Officielle - <?= htmlspecialchars($filiere) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root { 
            /* Alignement sur votre charte graphique */
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
            height: 32px; /* Légèrement rehaussé pour l'écriture manuscrite des notes */
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
            font-size: 13pt; 
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
        
        .group-label { 
            background-color: var(--fond-secondaire) !important; 
            color: var(--texte);
            font-weight: bold; 
            text-align: center; 
            font-size: 10pt;
            border: 1.5px solid var(--bordure) !important; 
        }

        .meta-text {
            font-size: 10pt;
            font-weight: bold;
            color: var(--texte-secondaire);
        }

        /* Configuration des largeurs de colonnes (Adapté pour 6 colonnes au total) */
        .w-num { width: 45px; text-align: center; }
        .w-nom { width: 220px; }
        .w-prenom { width: 320px; } /* Prénoms complets */
        .w-note { width: 100px; text-align: center; } /* Colonnes de notes */

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
            .group-label { background-color: #fbf4f0 !important; }
            .table-th { background-color: #e2e2e2 !important; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button class="btn-action btn-print" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Imprimer la liste de classe
        </button>
        <a href="classes.php" class="btn-action btn-back">
            <i class="fa-solid fa-arrow-left"></i> Retour à la gestion des étudiants
        </a>
    </div>

    <div class="page-container">
        <table class="excel-table">
            <tr>
                <td colspan="1" style="border:none"></td>
                <td colspan="3" class="dept-header">Département <?= htmlspecialchars($filiere) ?></td>
                <td colspan="2" style="border:none"></td>
            </tr>
            
            <tr><td colspan="6" style="border:none; height:12px;"></td></tr>

            <tr>
                <td colspan="2" style="border:none" class="meta-text"><i class="fa-solid fa-calendar-days"></i> Année Académique: <?= htmlspecialchars($annee_affichage) ?></td>
                <td colspan="4" style="border:none; text-align: right;" class="meta-text">Matière / ECUE : ___________________________</td>
            </tr>

            <tr>
                <td colspan="6" class="level-header">LISTE DE CLASSE : <?= htmlspecialchars($niveau) ?></td>
            </tr>

            

            <tr style="text-align: center;">
                <td class="w-num table-th">N°</td>
                <td class="w-nom table-th">Nom</td>
                <td class="w-prenom table-th">Prénom</td>
                <td class="w-note table-th"></td>
                <td class="w-note table-th"></td>
                <td class="w-note table-th"></td>
            </tr>

            <?php 
            if ($total > 0):
                foreach ($liste as $key => $row): 
            ?>
            <tr>
                <td class="text-center fw-bold"><?= $key + 1 ?></td>
                <td class="text-upper fw-bold"><?= htmlspecialchars($row['nom']) ?></td>
                <td style="text-transform: capitalize; white-space: normal; word-break: break-word; font-weight: 500;"><?= htmlspecialchars($row['prenom']) ?></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php 
                endforeach; 
            else:
                echo "<tr><td colspan='6' class='text-center fw-bold' style='height:40px; color:var(--texte-secondaire);'>Aucun étudiant inscrit dans cette classe</td></tr>";
            endif; 
            ?>

            <?php for($j=0; $j<6; $j++): ?>
            <tr>
                <td class="text-center fw-bold" style="background-color: var(--gris-impression) !important;"><?= $total + $j + 1 ?></td>
                <?php for($i=0; $i<5; $i++) echo '<td></td>'; ?>
            </tr>
            <?php endfor; ?>
        </table>

        <div class="list-footer">
            <span>Effectif total évalué : <?= $total ?> étudiant(s)</span>
            <span style="border-top: 1px solid #000; width: 280px; text-align: center; padding-top: 5px; margin-top: 15px;">Signature de l'Enseignant / Réf. Module</span>
        </div>
    </div>

</body>
</html>