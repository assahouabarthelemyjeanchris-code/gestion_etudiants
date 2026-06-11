<?php include "DB_connexion.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Université GDE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #fafefc; 
            --fg: #090b3c; 
            --muted: #573e4d; 
            --primary: #a21c3b; 
            --line: #d9d6df; 
            --success: #16a34a;
        }
        * { box-sizing: border-box; }
        body { 
            margin: 0; 
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, 'Helvetica Neue', Arial;
            color: var(--fg); 
            background: var(--bg);
            padding: 24px 12px;
        }
        .container { 
            max-width: 1100px; 
            margin: 0 auto; 
        }
        .mb-4 { 
            margin-bottom: 1.5rem; 
        }
        .form-container { 
            background: #fffffe; 
            border: 1px solid var(--line); 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(9, 11, 60, .08); 
            transition: all .3s ease; 
        }
        .header { 
            padding: 25px 20px; 
            border-bottom: 2px solid var(--primary); 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            background: linear-gradient(135deg, #fffffe 0%, #fbf4f0 100%);
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        h3 { 
            font-size: 24px; 
            margin: 0; 
            color: var(--primary); 
            font-weight: 700; 
            letter-spacing: -.5px; 
        }
        .btn-outline-secondary {
            background: #eef2ff;
            color: #1e40af;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all .3s ease;
            font-size: 14px;
        }
        .btn-outline-secondary:hover {
            background: var(--line);
            color: var(--fg);
            transform: translateY(-2px);
        }
        .form-content { 
            padding: 20px; 
        }
        fieldset {
            border: 1px solid var(--line) !important; 
            padding: 20px !important; 
            border-radius: 8px !important; 
            margin-bottom: 25px !important;
            animation: slideUp .4s ease;
        }
        legend { 
            font-weight: 700; 
            color: var(--primary); 
            padding: 0 10px;
            font-size: 15px;
            float: none;
            width: auto;
            margin-bottom: 0;
        }
        .grid-row { 
            display: grid; 
            grid-template-columns: repeat(12, 1fr); 
            gap: 15px; 
        }
        /* Mappage des anciennes colonnes Bootstrap vers notre grille CSS */
        .col-md-2 { grid-column: span 2; }
        .col-md-3 { grid-column: span 3; }
        .col-md-4 { grid-column: span 4; }
        
        .form-label { 
            font-size: 13px; 
            font-weight: 700; 
            margin-bottom: 6px; 
            display: block; 
            color: var(--fg); 
        }
        .text-danger {
            color: var(--primary) !important;
        }
        input[type="text"], input[type="email"], input[type="date"], input[type="number"], select, textarea {
            width: 100%; 
            border: 1px solid var(--line); 
            border-radius: 8px; 
            padding: 10px 12px; 
            font-size: 14px; 
            background: #fff; 
            transition: all .2s ease;
            color: inherit;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="date"]:focus, input[type="number"]:focus, select:focus, textarea:focus {
            border-color: var(--primary); 
            box-shadow: 0 0 0 3px rgba(162, 28, 59, .1); 
            outline: none;
        }
        .id-permanent-style { 
            background-color: #fff9db !important; 
            border: 2px solid #fab005 !important; 
            font-weight: bold; 
            color: #e67e22 !important; 
        }
        textarea { 
            min-height: 42px; 
            resize: vertical; 
            font-family: inherit; 
        }
        h6 { 
            font-weight: 800; 
            font-size: 14px; 
            background: linear-gradient(135deg, var(--primary), #391f20); 
            color: #fff; 
            border: none; 
            padding: 6px 10px; 
            border-radius: 6px;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .mb-2 {
            margin-bottom: 10px;
        }
        .border-end {
            border-right: 1px solid var(--line);
            padding-right: 15px;
        }
        .text-center { 
            text-align: center; 
        }
        .mt-5 { 
            margin-top: 30px; 
        }
        .btn-primary {
            padding: 12px 35px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(162, 28, 59, .2);
        }
        .btn-primary:hover {
            background: #391f20;
            transform: translateY(-2px);
            box-shadow: 0 6px 166px rgba(162, 28, 59, .3);
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        /* Styles pour les messages d'alerte */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background-color: #f0fdf4;
            color: var(--success);
            border: 1px solid #bbf7d0;
        }
        .alert-danger {
            background-color: #fef2f2;
            color: var(--primary);
            border: 1px solid #fecaca;
        }

        @media (max-width: 992px) {
            .col-md-4, .col-md-3, .col-md-2 { grid-column: span 6; }
            .border-end { border-right: none; padding-right: 0; border-bottom: 1px dashed var(--line); padding-bottom: 15px; margin-bottom: 15px; }
        }
        @media (max-width: 768px) {
            body { padding: 12px 6px; }
            h3 { font-size: 20px; }
            .form-content { padding: 15px; }
            fieldset { padding: 12px !important; }
            .col-md-4, .col-md-3, .col-md-2 { grid-column: span 12; }
            input, select, textarea { padding: 9px; font-size: 14px; }
            .btn-primary { width: 100%; }
        }

        /* --- CODE D'IMPRESSION CORRIGÉ POUR LES ENTRÉES --- */
        @media print {
            body {
                background: #fff;
                padding: 0;
                color: #000 !important;
            }
            .mb-4, .alert, .btn-primary, .mt-5, .header .btn-outline-secondary {
                display: none !important;
            }
            .form-container {
                border: none !important;
                box-shadow: none !important;
            }
            fieldset {
                page-break-inside: avoid;
                border: 1px solid #000 !important;
                margin-bottom: 20px !important;
            }
            /* Forcer l'affichage des valeurs à l'impression */
            input, select, textarea {
                border: none !important;
                border-bottom: 1px dotted #000 !important;
                background: transparent !important;
                box-shadow: none !important;
                padding: 4px 0 !important;
                color: #000 !important;
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="mb-4">
        <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="form-container">
        <div class="header">
            <h3><i class="fas fa-user-graduate me-2"></i> Formulaire d'inscription étudiant</h3>
        </div>

        <div class="form-content">
            <form action="req/inscription.php" method="POST">

                <fieldset>
                    <legend>1. ÉTAT CIVIL & IDENTITÉ</legend>
                    <div class="grid-row">
                        <div class="col-md-4">
                            <label class="form-label text-danger">ID Permanent</label>
                            <input type="text" 
                                   name="id_permanent" 
                                   class="form-control id-permanent-style" 
                                   pattern="[A-Za-z]{4}[0-9]{10}" 
                                   maxlength="14"
                                   style="text-transform: uppercase;"
                                   oninput="this.value = this.value.toUpperCase()"
                                   title="L'ID permanent doit contenir exactement 4 lettres suivies de 10 chiffres (Ex: ABCD1234567890)"
                                   required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nom</label>
                            <input type="text" name="nom" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prénom(s)</label>
                            <input type="text" name="prenom" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sexe</label>
                            <select name="sexe" required>
                                <option value="Masculin">Masculin</option>
                                <option value="Féminin">Féminin</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" name="date_naissance" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Lieu de naissance</label>
                            <input type="text" name="lieu_naissance">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nationalité</label>
                            <input type="text" name="nationalite">
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>2. INFORMATIONS ACADÉMIQUES</legend>
                    <div class="grid-row">
                        <div class="col-md-4">
                            <label class="form-label">Filière</label>
                            <select name="id_filiere" required>
                                <option value="">Sélectionner...</option>
                                <?php 
                                $res = $conn->query("SELECT id_filiere, nom_filiere FROM filiere ORDER BY nom_filiere");
                                while($f = $res->fetch()) echo "<option value='".$f['id_filiere']."'>".$f['nom_filiere']."</option>";
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Niveau</label>
                            <input type="text" name="niveau" placeholder="Ex: Licence 1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Régime de cours</label>
                            <select name="cours">
                                <option value="Jour">Jour</option>
                                <option value="Soir">Soir</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Niveau d'étude</label>
                            <input type="text" name="niveau_etude" placeholder="Ex: L1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Établissement d'origine</label>
                            <input type="text" name="etablissement_origine">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Affecté ?</label>
                            <select name="affecte">
                                <option value="Non">Non</option>
                                <option value="Oui">Oui</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Redoublant ?</label>
                            <select name="redoublant">
                                <option value="Non">Non</option>
                                <option value="Oui">Oui</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>3. DIPLÔME BAC & IDENTITÉ</legend>
                    <div class="grid-row">
                        <div class="col-md-3"><label class="form-label">Diplôme</label><input type="text" name="diplome" value="BAC"></div>
                        <div class="col-md-2"><label class="form-label">Série</label><input type="text" name="serie_diplome"></div>
                        <div class="col-md-2"><label class="form-label">Points</label><input type="number" name="points_diplome"></div>
                        <div class="col-md-2"><label class="form-label">Année BAC</label><input type="number" name="annee_diplome"></div>
                        <div class="col-md-3"><label class="form-label">Matricule BAC</label><input type="text" name="n_matricule_bac"></div>
                        <div class="col-md-3"><label class="form-label">N° Table BAC</label><input type="text" name="n_table_bac"></div>
                        <div class="col-md-3"><label class="form-label">N° CNI</label><input type="text" name="n_carte_identite"></div>
                        <div class="col-md-3"><label class="form-label">Délivrée le</label><input type="date" name="date_carte_identite"></div>
                        <div class="col-md-3"><label class="form-label">N° CMU</label><input type="text" name="n_cmu"></div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>4. CONTACTS & SANTÉ</legend>
                    <div class="grid-row">
                        <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" required></div>
                        <div class="col-md-4"><label class="form-label">Téléphone</label><input type="text" name="contact_etudiant"></div>
                        <div class="col-md-4"><label class="form-label">Adresse / Domicile</label><input type="text" name="adresse" required></div>
                        
                        <div class="col-md-2"><label class="form-label">Asthme</label><select name="asthme"><option value="Non">Non</option><option value="Oui">Oui</option></select></div>
                        <div class="col-md-2"><label class="form-label">Hypertension</label><select name="hypertension"><option value="Non">Non</option><option value="Oui">Oui</option></select></div>
                        <div class="col-md-2"><label class="form-label">Psychique</label><select name="problemes_psychiques"><option value="Non">Non</option><option value="Oui">Oui</option></select></div>
                        <div class="col-md-2"><label class="form-label">Handicap</label><select name="handicap_physique"><option value="Non">Non</option><option value="Oui">Oui</option></select></div>
                        <div class="col-md-4"><label class="form-label">Observations médicales</label><textarea name="autres_infos_sante" rows="1"></textarea></div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>5. PARENTS & TUTEUR</legend>
                    <div class="grid-row">
                        <div class="col-md-4 border-end">
                            <h6>PÈRE</h6>
                            <input type="text" name="nom_prenom_pere" class="mb-2" placeholder="Nom complet">
                            <input type="text" name="profession_pere" class="mb-2" placeholder="Profession">
                            <input type="text" name="telephone_pere" class="mb-2" placeholder="Téléphone">
                            <input type="text" name="domicile_pere" placeholder="Domicile">
                        </div>
                        <div class="col-md-4 border-end">
                            <h6>MÈRE</h6>
                            <input type="text" name="nom_prenom_mere" class="mb-2" placeholder="Nom complet">
                            <input type="text" name="profession_mere" class="mb-2" placeholder="Profession">
                            <input type="text" name="telephone_mere" class="mb-2" placeholder="Téléphone">
                            <input type="text" name="domicile_mere" placeholder="Domicile">
                        </div>
                        <div class="col-md-4">
                            <h6>TUTEUR LÉGAL</h6>
                            <input type="text" name="tuteur_legal" class="mb-2" placeholder="Nom complet" required>
                            <input type="text" name="profession_tuteur" class="mb-2" placeholder="Profession">
                            <input type="text" name="telephone_tuteur" class="mb-2" placeholder="Téléphone" required>
                            <input type="text" name="domicile_tuteur" placeholder="Domicile">
                        </div>
                    </div>
                </fieldset>

                <div class="mt-5 text-center">
                    <button type="submit" class="btn-primary">VALIDER L'INSCRIPTION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            const alertContainer = document.querySelector('.alert-success');
            if (alertContainer) {
                const printBtn = document.createElement('button');
                printBtn.innerHTML = '<i class="fas fa-print"></i> Imprimer ma fiche d\'inscription';
                printBtn.style.cssText = `
                    margin-left: auto;
                    background-color: #16a34a;
                    color: white;
                    border: none;
                    padding: 8px 16px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 13px;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    transition: background 0.2s;
                `;
                
                printBtn.onmouseover = () => printBtn.style.backgroundColor = '#15803d';
                printBtn.onmouseout = () => printBtn.style.backgroundColor = '#16a34a';
                
                printBtn.addEventListener('click', function() {
                    window.print();
                });
                
                alertContainer.appendChild(printBtn);
            }
        }
    });
</script>
</body>
</html>