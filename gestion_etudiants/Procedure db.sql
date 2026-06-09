DELIMITER //

-- AJOUTER une classe
CREATE PROCEDURE ps_ajouter_classe(IN p_nom VARCHAR(50), IN p_niveau VARCHAR(20), IN p_capa INT)
BEGIN
    INSERT INTO classes(nom_classe, niveau, capacite_max) VALUES (p_nom, p_niveau, p_capa);
END //

-- MODIFIER une classe
CREATE PROCEDURE ps_modifier_classe(IN p_id INT, IN p_nom VARCHAR(50), IN p_niveau VARCHAR(20), IN p_capa INT)
BEGIN
    UPDATE classes SET nom_classe = p_nom, niveau = p_niveau, capacite_max = p_capa WHERE id = p_id;
END //

-- SUPPRIMER une classe (Sécurité : empêche si des étudiants y sont inscrits)
CREATE PROCEDURE ps_supprimer_classe(IN p_id INT)
BEGIN
    DELETE FROM classes WHERE id = p_id;
END //

-- AFFICHER toutes les classes
CREATE PROCEDURE ps_afficher_classes()
BEGIN
    SELECT * FROM classes ORDER BY niveau, nom_classe;
END //

DELIMITER ;
DELIMITER //

-- AJOUTER un étudiant
CREATE PROCEDURE ps_ajouter_etudiant(IN p_mat VARCHAR(20), IN p_nom VARCHAR(50), IN p_prenom VARCHAR(50), IN p_dnaiss DATE, IN p_mail VARCHAR(100), IN p_tel VARCHAR(20))
BEGIN
    INSERT INTO etudiants(matricule, nom, prenom, date_naissance, email, telephone) 
    VALUES (p_mat, p_nom, p_prenom, p_dnaiss, p_mail, p_tel);
END //

-- MODIFIER un étudiant
CREATE PROCEDURE ps_modifier_etudiant(IN p_id INT, IN p_nom VARCHAR(50), IN p_prenom VARCHAR(50), IN p_mail VARCHAR(100), IN p_tel VARCHAR(20))
BEGIN
    UPDATE etudiants SET nom = p_nom, prenom = p_prenom, email = p_mail, telephone = p_tel WHERE id = p_id;
END //

-- SUPPRIMER un étudiant
CREATE PROCEDURE ps_supprimer_etudiant(IN p_id INT)
BEGIN
    DELETE FROM etudiants WHERE id = p_id;
END //

-- AFFICHER tous les étudiants
CREATE PROCEDURE ps_afficher_etudiants()
BEGIN
    SELECT * FROM etudiants ORDER BY nom ASC;
END //

DELIMITER ;
DELIMITER //

-- INSCRIRE un étudiant (avec vérification de la capacité max de la classe)
CREATE PROCEDURE ps_inscrire_etudiant(IN p_etudiant_id INT, IN p_classe_id INT, IN p_annee VARCHAR(10))
BEGIN
    DECLARE v_actuel INT;
    DECLARE v_max INT;

    -- Récupérer le nombre d'inscrits et la capacité
    SELECT COUNT(*) INTO v_actuel FROM inscriptions WHERE classe_id = p_classe_id AND annee_scolaire = p_annee;
    SELECT capacite_max INTO v_max FROM classes WHERE id = p_classe_id;

    IF v_actuel < v_max THEN
        INSERT INTO inscriptions(etudiant_id, classe_id, annee_scolaire) 
        VALUES (p_etudiant_id, p_classe_id, p_annee);
    ELSE
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La classe a atteint sa capacité maximale.';
    END IF;
END //

-- MODIFIER une inscription (changer de classe ou de statut)
CREATE PROCEDURE ps_modifier_inscription(IN p_id INT, IN p_classe_id INT, IN p_statut VARCHAR(20))
BEGIN
    UPDATE inscriptions SET classe_id = p_classe_id, statut = p_statut WHERE id = p_id;
END //

-- SUPPRIMER/ANNULER une inscription
CREATE PROCEDURE ps_supprimer_inscription(IN p_id INT)
BEGIN
    DELETE FROM inscriptions WHERE id = p_id;
END //

-- AFFICHER LA LISTE DE CLASSE (La requête la plus importante)
CREATE PROCEDURE ps_liste_par_classe(IN p_classe_id INT, IN p_annee VARCHAR(10))
BEGIN
    SELECT 
        e.matricule, 
        e.nom, 
        e.prenom, 
        e.telephone,
        i.date_inscription, 
        i.statut
    FROM etudiants e
    JOIN inscriptions i ON e.id = i.etudiant_id
    WHERE i.classe_id = p_classe_id AND i.annee_scolaire = p_annee
    ORDER BY e.nom ASC;
END //

DELIMITER ;