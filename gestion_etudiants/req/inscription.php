<?php
session_start();
require_once "../DB_connexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Préparation des données
    $annee_actuelle = date('Y');
    $annee_suivante = $annee_actuelle + 1;
    $annee_academique = $annee_actuelle . "-" . $annee_suivante; // Génère ex: 2025-2026

    try {
        // Début de la transaction
        $conn->beginTransaction();

        // 2. Insertion dans la table 'etudiants'
        $sql_etudiant = "INSERT INTO etudiants (
            id_permanent, nom, prenom, sexe, date_naissance, lieu_naissance, nationalite, 
            email, contact_etudiant, adresse, id_filiere, niveau, etablissement_origine, 
            niveau_etude, cours, affecte, redoublant, diplome, serie_diplome, points_diplome, 
            annee_diplome, n_matricule_bac, n_table_bac, n_cmu, n_carte_identite, date_carte_identite, 
            asthme, hypertension, problemes_psychiques, handicap_physique, autres_infos_sante, 
            nom_prenom_pere, profession_pere, telephone_pere, domicile_pere, 
            nom_prenom_mere, profession_mere, telephone_mere, domicile_mere, 
            tuteur_legal, profession_tuteur, telephone_tuteur, domicile_tuteur
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
        )";

        $stmt_etudiant = $conn->prepare($sql_etudiant);
        $stmt_etudiant->execute([
            $_POST['id_permanent'], $_POST['nom'], $_POST['prenom'], $_POST['sexe'], $_POST['date_naissance'],
            $_POST['lieu_naissance'], $_POST['nationalite'], $_POST['email'], $_POST['contact_etudiant'],
            $_POST['adresse'], $_POST['id_filiere'], $_POST['niveau'], $_POST['etablissement_origine'],
            $_POST['niveau_etude'], $_POST['cours'], $_POST['affecte'], $_POST['redoublant'],
            $_POST['diplome'], $_POST['serie_diplome'], $_POST['points_diplome'] ?: 0,
            $_POST['annee_diplome'] ?: null, $_POST['n_matricule_bac'], $_POST['n_table_bac'],
            $_POST['n_cmu'], $_POST['n_carte_identite'], $_POST['date_carte_identite'] ?: null,
            $_POST['asthme'], $_POST['hypertension'], $_POST['problemes_psychiques'],
            $_POST['handicap_physique'], $_POST['autres_infos_sante'], $_POST['nom_prenom_pere'],
            $_POST['profession_pere'], $_POST['telephone_pere'], $_POST['domicile_pere'],
            $_POST['nom_prenom_mere'], $_POST['profession_mere'], $_POST['telephone_mere'],
            $_POST['domicile_mere'], $_POST['tuteur_legal'], $_POST['profession_tuteur'],
            $_POST['telephone_tuteur'], $_POST['domicile_tuteur']
        ]);

        // 3. Récupérer l'ID de l'étudiant qui vient d'être créé
        $last_id_etudiant = $conn->lastInsertId();

        // 4. Insertion dans la table 'inscription'
        $sql_inscription = "INSERT INTO inscription (id_etudiants, id_filiere, annee_academique, statut_inscription) 
                            VALUES (?, ?, ?, 'Validée')";
        
        $stmt_ins = $conn->prepare($sql_inscription);
        $stmt_ins->execute([
            $last_id_etudiant, 
            $_POST['id_filiere'], 
            $annee_academique
        ]);

        // Valider la transaction
        $conn->commit();

        // Redirection vers une page de succès ou la fiche d'impression
        header("Location: ../imprimer_fiche.php?id=" . $last_id_etudiant . "&success=Inscription réussie");
        exit;

    } catch (Exception $e) {
        // En cas d'erreur, on annule tout ce qui a été fait
        $conn->rollBack();
        die("Erreur lors de l'inscription : " . $e->getMessage());
    }
}