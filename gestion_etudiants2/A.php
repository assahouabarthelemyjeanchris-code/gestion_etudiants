<?php
include "../DB_connexion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Utilisation de l'opérateur ?? pour éviter les erreurs "Undefined array key"
    // 1. État Civil
    $nom              = $_POST['nom'] ?? null;
    $prenom           = $_POST['prenom'] ?? null;
    $sexe             = $_POST['sexe'] ?? null;
    $date_naissance   = $_POST['date_naissance'] ?? null;
    $lieu_naissance   = $_POST['lieu_naissance'] ?? null;
    $nationalite      = $_POST['nationalite'] ?? null;
    $n_carte_identite = $_POST['n_carte_identite'] ?? null;
    
    // 2. Coordonnées
    $email            = $_POST['email'] ?? null;
    $contact_etudiant = $_POST['contact_etudiant'] ?? null;
    $adresse          = $_POST['adresse'] ?? null;
    
    // 3. Académique
    $id_filiere       = $_POST['id_filiere'] ?? null;
    $niveau           = $_POST['niveau'] ?? null;
    $cours            = $_POST['cours'] ?? 'Jour';
    $etablissement_origine = $_POST['etablissement_origine'] ?? null;
    $affecte          = $_POST['affecte'] ?? 'Non';
    $redoublant       = $_POST['redoublant'] ?? 'Non';
    
    // 4. Diplôme
    $diplome          = $_POST['diplome'] ?? null;
    $serie_diplome    = $_POST['serie_diplome'] ?? null;
    $annee_diplome    = $_POST['annee_diplome'] ?? null;
    $n_matricule_bac  = $_POST['n_matricule_bac'] ?? null;
    
    // 5. Santé
    $asthme           = $_POST['asthme'] ?? 'Non';
    $hypertension     = $_POST['hypertension'] ?? 'Non';
    $autres_infos_sante = $_POST['autres_infos_sante'] ?? null;
    
    // 6. PARENTS (C'est ici que se trouvaient les erreurs)
    // On récupère les nouveaux noms du formulaire
    $nom_prenom_pere  = $_POST['nom_prenom_pere'] ?? null;
    $telephone_pere   = $_POST['telephone_pere'] ?? null;
    $nom_prenom_mere  = $_POST['nom_prenom_mere'] ?? null;
    $telephone_mere   = $_POST['telephone_mere'] ?? null;
    $tuteur_legal     = $_POST['tuteur_legal'] ?? null;
    $telephone_tuteur = $_POST['telephone_tuteur'] ?? null;

    // Vérification des champs critiques
    if (empty($nom) || empty($prenom) || empty($email) || empty($tuteur_legal)) {
        header("Location: ../inscription_view.php?error=Champs obligatoires manquants.");
        exit;
    }

    try {
        $sql = "INSERT INTO etudiants (
                    nom, prenom, sexe, date_naissance, lieu_naissance, nationalite, n_carte_identite,
                    email, contact_etudiant, adresse, 
                    id_filiere, niveau, cours, etablissement_origine, affecte, redoublant,
                    diplome, serie_diplome, annee_diplome, n_matricule_bac,
                    asthme, hypertension, autres_infos_sante,
                    nom_prenom_pere, telephone_pere, nom_prenom_mere, telephone_mere,
                    tuteur_legal, telephone_tuteur
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $nom, $prenom, $sexe, $date_naissance, $lieu_naissance, $nationalite, $n_carte_identite,
            $email, $contact_etudiant, $adresse,
            $id_filiere, $niveau, $cours, $etablissement_origine, $affecte, $redoublant,
            $diplome, $serie_diplome, $annee_diplome, $n_matricule_bac,
            $asthme, $hypertension, $autres_infos_sante,
            $nom_prenom_pere, $telephone_pere, $nom_prenom_mere, $telephone_mere,
            $tuteur_legal, $telephone_tuteur
        ]);

        $last_id = $conn->lastInsertId();
        header("Location: ../imprimer_fiche.php?id=" . $last_id);
        exit;

    } catch (PDOException $e) {
        header("Location: ../inscription_view.php?error=" . urlencode($e->getMessage()));
        exit;
    }
}