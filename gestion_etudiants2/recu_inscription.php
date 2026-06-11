<?php 
include "DB_connexion.php"; 

// On vérifie si l'identifiant est bien présent dans l'URL
if (!isset($_GET['id_permanent'])) {
    header("Location: inscription.php");
    exit();
}

$id_permanent = $_GET['id_permanent'];

// Récupération de toutes les données de l'étudiant
$query = $conn->prepare("SELECT * FROM etudiants WHERE id_permanent = ?");
$query->execute([$id_permanent]);
$etudiant = $query->fetch();

// Si l'étudiant n'existe pas, retour au formulaire
if (!$etudiant) {
    header("Location: inscription.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche d'Inscription Officielle - Université GDE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #fafefc; 
            --fg: #090b3c; 
            --primary: #a21c3b; 
            --line: #d9d6df; 
        }
        body { 
            font-family: system-ui, -apple-system, sans-serif;
            color: var(--fg); 
            background: var(--bg);
            padding: 30px 20px;
            margin: 0;
        }
        .container { max-width: 900px; margin: 0 auto; }
        .fiche {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .header-fiche {
            text-align: center;
            border-bottom: 3px double var(--primary);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        h2 { color: var(--primary); margin: 5px 0; }
        .section-title {
            background: #f8fafc;
            color: var(--primary);
            padding: 6px 12px;
            font-weight: bold;
            border-left: 4px solid var(--primary);
            margin-top: 20px;
            margin-bottom: 12px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px 20px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 20px;
        }
        .info-group {
            border-bottom: 1px dotted var(--line);
            padding-bottom: 4px;
        }
        .label { font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .value { font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 2px; }
        .actions { text-align: center; margin-bottom: 20px; }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-print { background: #16a34a; color: white; }
        .btn-print:hover { background: #15803d; }
        .btn-back { background: #e2e8f0; color: #475569; margin-right: 10px; }
        .btn-back:hover { background: #cbd5e1; }
        
        .parent-box {
            border: 1px dashed var(--line);
            padding: 12px;
            border-radius: 6px;
            background: #fbfbfb;
        }
        .parent-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 2px;
        }

        @media print {
            body { background: white; padding: 0; }
            .fiche { border: none; box-shadow: none; padding: 0; }
            .actions { display: none; }
            .section-title { background: #eee !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="actions">
        <a href="inscription_view.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Retour au formulaire</a>
        <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Imprimer la fiche complète</button>
    </div>

    <div class="fiche">
        <div class="header-fiche">
            <h2>UNIVERSITÉ INTERNATIONALE DE YAMASSOUKRO (UIYA)</h2>
            <p style="letter-spacing: 2px; font-weight: 600; margin: 0; color: #64748b;">FICHE COMPLÈTE D'INSCRIPTION</p>
            <p style="font-size: 13px; margin-top: 5px; font-weight: bold;">Statut : <?php echo htmlspecialchars($etudiant['statut_inscription'] ?? 'En attente'); ?></p>
        </div>

        <div class="section-title">1. État Civil & Identité</div>
        <div class="grid">
            <div class="info-group">
                <div class="label">ID Permanent</div>
                <div class="value" style="color: #e67e22;"><?php echo htmlspecialchars($etudiant['id_permanent'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Nom</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['nom'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Prénom(s)</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['prenom'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Sexe</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['sexe'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Date de Naissance</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['date_naissance'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Lieu de Naissance</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['lieu_naissance'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Nationalité</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['nationalite'] ?? '-'); ?></div>
            </div>
        </div>

        <div class="section-title">2. Informations Académiques</div>
        <div class="grid">
            <div class="info-group">
                <div class="label">Niveau</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['niveau'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Niveau d'étude</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['niveau_etude'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Régime de cours</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['cours'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Établissement d'origine</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['etablissement_origine'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Affecté de l'État</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['affecte'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Redoublant</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['redoublant'] ?? ''); ?></div>
            </div>
        </div>

        <div class="section-title">3. Diplôme BAC & Pièces d'identité</div>
        <div class="grid">
            <div class="info-group">
                <div class="label">Diplôme</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['diplome'] ?? 'BAC'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Série</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['serie_diplome'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Points obtenus</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['points_diplome'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Année d'obtention</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['annee_diplome'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Matricule BAC</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['n_matricule_bac'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">N° Table BAC</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['n_table_bac'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">N° CNI / Attestation</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['n_carte_identite'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Date délivrance CNI</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['date_carte_identite'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">N° CMU</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['n_cmu'] ?? '-'); ?></div>
            </div>
        </div>

        <div class="section-title">4. Contacts & Santé</div>
        <div class="grid">
            <div class="info-group">
                <div class="label">Email</div>
                <div class="value" style="font-size: 13px;"><?php echo htmlspecialchars($etudiant['email'] ?? ''); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Téléphone Étudiant</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['contact_etudiant'] ?? '-'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Adresse / Domicile</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['adresse'] ?? ''); ?></div>
            </div>
        </div>
        <div class="grid" style="margin-top: 10px;">
            <div class="info-group">
                <div class="label">Asthmatique</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['asthme'] ?? 'Non'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Hypertension</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['hypertension'] ?? 'Non'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Troubles Psychiques</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['problemes_psychiques'] ?? 'Non'); ?></div>
            </div>
            <div class="info-group">
                <div class="label">Handicap Physique</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['handicap_physique'] ?? 'Non'); ?></div>
            </div>
            <div class="info-group" style="grid-column: span 2;">
                <div class="label">Observations Médicales</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['autres_infos_sante'] ?? 'Aucune'); ?></div>
            </div>
        </div>

        <div class="section-title">5. Filiation & Tuteurs</div>
        <div class="grid">
            <div class="parent-box">
                <div class="parent-title">PÈRE</div>
                <div class="label">Nom complet</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['nom_prenom_pere'] ?? '-'); ?></div>
                <br>
                <div class="label">Profession</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['profession_pere'] ?? '-'); ?></div>
                <br>
                <div class="label">Téléphone</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['telephone_pere'] ?? '-'); ?></div>
                <br>
                <div class="label">Domicile</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['domicile_pere'] ?? '-'); ?></div>
            </div>

            <div class="parent-box">
                <div class="parent-title">MÈRE</div>
                <div class="label">Nom complet</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['nom_prenom_mere'] ?? '-'); ?></div>
                <br>
                <div class="label">Profession</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['profession_mere'] ?? '-'); ?></div>
                <br>
                <div class="label">Téléphone</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['telephone_mere'] ?? '-'); ?></div>
                <br>
                <div class="label">Domicile</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['domicile_mere'] ?? '-'); ?></div>
            </div>

            <div class="parent-box">
                <div class="parent-title">TUTEUR LÉGAL</div>
                <div class="label">Nom complet</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['tuteur_legal'] ?? '-'); ?></div>
                <br>
                <div class="label">Profession</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['profession_tuteur'] ?? '-'); ?></div>
                <br>
                <div class="label">Téléphone</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['telephone_tuteur'] ?? '-'); ?></div>
                <br>
                <div class="label">Domicile</div>
                <div class="value"><?php echo htmlspecialchars($etudiant['domicile_tuteur'] ?? '-'); ?></div>
            </div>
        </div>

        <div class="section-title">6. Cadre réservé à l'Administration</div>
        <div class="grid-2" style="margin-top: 15px;">
            <div style="border: 1px dashed var(--line); height: 110px; padding: 10px; font-size: 11px; color: #64748b;">
                <strong>Signature de l'Étudiant(e)</strong>
                <p style="font-size: 10px; margin-top: 5px; color: #94a3b8;">Précédée de la mention "Lu et approuvé"</p>
            </div>
            <div style="border: 1px dashed var(--line); height: 110px; padding: 10px; font-size: 11px; color: #64748b;">
                <strong>Cachet et Validation du RESPONSABLE DU DEPARTEMENT</strong>
                <p style="font-size: 10px; margin-top: 5px; color: #94a3b8;">Date : .... / .... / 2026</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>