<?php
session_start();
include_once "../DB_connexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Récupération des données du formulaire
    $id_permanent = htmlspecialchars($_POST['id_permanent']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $sexe = $_POST['sexe'];
    $date_naissance = $_POST['date_naissance'];
    $lieu_naissance = htmlspecialchars($_POST['lieu_naissance']);
    $nationalite = htmlspecialchars($_POST['nationalite']);
    
    $id_filiere = intval($_POST['id_filiere']);
    $niveau = htmlspecialchars($_POST['niveau']);
    $cours = $_POST['cours'];
    $niveau_etude = htmlspecialchars($_POST['niveau_etude']);
    $etablissement_origine = htmlspecialchars($_POST['etablissement_origine']);
    $affecte = $_POST['affecte'];
    $redoublant = $_POST['redoublant'];
    
    $diplome = htmlspecialchars($_POST['diplome']);
    $serie_diplome = htmlspecialchars($_POST['serie_diplome']);
    $points_diplome = !empty($_POST['points_diplome']) ? intval($_POST['points_diplome']) : null;
    $annee_diplome = !empty($_POST['annee_diplome']) ? intval($_POST['annee_diplome']) : null;
    $n_matricule_bac = htmlspecialchars($_POST['n_matricule_bac']);
    $n_table_bac = htmlspecialchars($_POST['n_table_bac']);
    $n_carte_identite = htmlspecialchars($_POST['n_carte_identite']);
    $date_carte_identite = !empty($_POST['date_carte_identite']) ? $_POST['date_carte_identite'] : null;
    $n_cmu = htmlspecialchars($_POST['n_cmu']);
    
    $email = htmlspecialchars($_POST['email']);
    $contact_etudiant = htmlspecialchars($_POST['contact_etudiant']);
    $adresse = htmlspecialchars($_POST['adresse']);
    
    $asthme = $_POST['asthme'];
    $hypertension = $_POST['hypertension'];
    $problemes_psychiques = $_POST['problemes_psychiques'];
    $handicap_physique = $_POST['handicap_physique'];
    $autres_infos_sante = htmlspecialchars($_POST['autres_infos_sante']);
    
    $nom_prenom_pere = htmlspecialchars($_POST['nom_prenom_pere']);
    $profession_pere = htmlspecialchars($_POST['profession_pere']);
    $telephone_pere = htmlspecialchars($_POST['telephone_pere']);
    $domicile_pere = htmlspecialchars($_POST['domicile_pere']);
    
    $nom_prenom_mere = htmlspecialchars($_POST['nom_prenom_mere']);
    $profession_mere = htmlspecialchars($_POST['profession_mere']);
    $telephone_mere = htmlspecialchars($_POST['telephone_mere']);
    $domicile_mere = htmlspecialchars($_POST['domicile_mere']);
    
    $tuteur_legal = htmlspecialchars($_POST['tuteur_legal']);
    $profession_tuteur = htmlspecialchars($_POST['profession_tuteur']);
    $telephone_tuteur = htmlspecialchars($_POST['telephone_tuteur']);
    $domicile_tuteur = htmlspecialchars($_POST['domicile_tuteur']);

    // Par défaut, l'étudiant commence avec le statut 'En attente'
    $statut_inscription = "En attente";

    try {
        $conn->beginTransaction();

        // Récupération de l'année universitaire active globale depuis la configuration
        try {
            $annee_academique = $conn->query("SELECT valeur FROM configuration_gde WHERE cle = 'annee_universitaire_active'")->fetchColumn();
            if (!$annee_academique) {
                $annee_academique = date('Y') . "-" . (date('Y') + 1);
            }
        } catch (Exception $e) {
            $annee_academique = date('Y') . "-" . (date('Y') + 1);
        }

        // 1. Insertion dans la table etudiants (Exactement 44 colonnes nettes)
        $sql_etudiant = "INSERT INTO etudiants (
            id_permanent, prenom, nom, niveau, id_filiere, etablissement_origine, niveau_etude, cours, affecte, redoublant,
            diplome, serie_diplome, points_diplome, annee_diplome, n_matricule_bac, n_table_bac, asthme, hypertension,
            problemes_psychiques, handicap_physique, autres_infos_sante, nom_prenom_pere, telephone_pere, adresse, sexe,
            email, contact_etudiant, n_carte_identite, date_carte_identite, n_cmu, date_naissance, lieu_naissance, nationalite,
            profession_pere, domicile_pere, nom_prenom_mere, profession_mere, telephone_mere, domicile_mere, tuteur_legal, 
            profession_tuteur, telephone_tuteur, domicile_tuteur, statut_inscription
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?
        )";

        $stmt = $conn->prepare($sql_etudiant);
        $stmt->execute([
            $id_permanent, $prenom, $nom, $niveau, $id_filiere, $etablissement_origine, $niveau_etude, $cours, $affecte, $redoublant,
            $diplome, $serie_diplome, $points_diplome, $annee_diplome, $n_matricule_bac, $n_table_bac, $asthme, $hypertension,
            $problemes_psychiques, $handicap_physique, $autres_infos_sante, $nom_prenom_pere, $telephone_pere, $adresse, $sexe,
            $email, $contact_etudiant, $n_carte_identite, $date_carte_identite, $n_cmu, $date_naissance, $lieu_naissance, $nationalite,
            $profession_pere, $domicile_pere, $nom_prenom_mere, $profession_mere, $telephone_mere, $domicile_mere, $tuteur_legal,
            $profession_tuteur, $telephone_tuteur, $domicile_tuteur, $statut_inscription
        ]);

        $id_nouvel_etudiant = $conn->lastInsertId();

        // 2. Insertion dans la table inscription
        $sql_ins = "INSERT INTO inscription (id_etudiants, id_filiere, annee_academique, statut_inscription) VALUES (?, ?, ?, 'En attente')";
        $stmt_ins = $conn->prepare($sql_ins);
        $stmt_ins->execute([$id_nouvel_etudiant, $id_filiere, $annee_academique]);

        // 3. Récupération du responsable de département lié à la filière choisie
        $stmt_find_resp = $conn->prepare("
            SELECT d.id_responsable, f.nom_filiere 
            FROM filiere f
            JOIN departement d ON f.id_departement = d.id_departement
            WHERE f.id_filiere = ? LIMIT 1
        ");
        $stmt_find_resp->execute([$id_filiere]);
        $infos_filiere = $stmt_find_resp->fetch();

        $nom_complet = $prenom . " " . $nom;

        if ($infos_filiere && !empty($infos_filiere['id_responsable'])) {
            $id_resp = $infos_filiere['id_responsable'];
            $nom_filiere_msg = $infos_filiere['nom_filiere'];
            
            // Notification pour le Responsable du Département ciblée
            $msg_resp = "Nouvelle inscription à vérifier : L'étudiant(e) $nom_complet s'est inscrit en $nom_filiere_msg (ID: $id_nouvel_etudiant) et attend votre validation.";
            $ins_notif_resp = $conn->prepare("INSERT INTO notifications (destinataire_role, id_destinataire_specifique, message, statut_lecture, created_at) VALUES ('Responsable', ?, ?, 0, NOW())");
            $ins_notif_resp->execute([$id_resp, $msg_resp]);
        }

        // 4. Notification globale pour l'Administrateur
        $msg_admin = "L'étudiant(e) $nom_complet a soumis son dossier d'inscription en ligne (ID: $id_nouvel_etudiant). En attente de validation.";
        $ins_notif_admin = $conn->prepare("INSERT INTO notifications (destinataire_role, message, statut_lecture, created_at) VALUES ('Admin', ?, 0, NOW())");
        $ins_notif_admin->execute([$msg_admin]);

        $conn->commit();
        
        // Redirection modifiée vers le reçu d'inscription avec l'ID Permanent
        header("Location: ../recu_inscription.php?id_permanent=" . urlencode($id_permanent));
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: ../inscription_view.php?error=" . urlencode("Erreur lors de l'inscription : " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../inscription_view.php");
    exit;
}
?>