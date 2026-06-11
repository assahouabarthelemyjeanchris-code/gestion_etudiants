-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 11 juin 2026 à 04:20
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_etudiants2`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

DROP TABLE IF EXISTS `administrateur`;
CREATE TABLE IF NOT EXISTS `administrateur` (
  `administrateur_id` int NOT NULL AUTO_INCREMENT,
  `nomutilisateur` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `motsdepasse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`administrateur_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`administrateur_id`, `nomutilisateur`, `motsdepasse`, `nom`, `email`, `prenom`, `reset_token`) VALUES
(1, 'chris', '$2y$10$elG8Q8MYheDfQoAWDqCHnuCrTvs7.BgEn7/tk.A3D.GDl4QO0pMXi', 'Chris', 'jeanchristassahoua7@gmail.com', 'Assahoua', '1092d61cc4b1a38e9d7dc48b9d7db5d6'),
(2, 'A', '$2y$10$oT8k1l/ZxDWJgO0I4mqt1eyKz215c.MGY5sHVnAtlDI0G2VvLMVJG', 'A', 'A@gmail.com', 'A', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `configuration_gde`
--

DROP TABLE IF EXISTS `configuration_gde`;
CREATE TABLE IF NOT EXISTS `configuration_gde` (
  `cle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`cle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `configuration_gde`
--

INSERT INTO `configuration_gde` (`cle`, `valeur`) VALUES
('annee_universitaire_active', '2026-2027');

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `id_departement` int NOT NULL AUTO_INCREMENT,
  `nom_departement` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_responsable` int DEFAULT NULL,
  PRIMARY KEY (`id_departement`),
  KEY `fk_departement_responsable` (`id_responsable`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`id_departement`, `nom_departement`, `id_responsable`) VALUES
(1, 'Sciences Technologies de l\'Information et Génie Informatique', 5),
(2, 'Sciences Économiques et de Gestion', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
CREATE TABLE IF NOT EXISTS `etudiants` (
  `id_etudiants` int NOT NULL AUTO_INCREMENT,
  `id_permanent` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prenom` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `niveau` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_filiere` int NOT NULL,
  `etablissement_origine` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `niveau_etude` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cours` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `affecte` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `redoublant` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diplome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serie_diplome` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `points_diplome` int DEFAULT NULL,
  `annee_diplome` year DEFAULT NULL,
  `n_matricule_bac` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `n_table_bac` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asthme` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hypertension` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `problemes_psychiques` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `handicap_physique` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `autres_infos_sante` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `nom_prenom_pere` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone_pere` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sexe` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contact_etudiant` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `n_carte_identite` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_carte_identite` date DEFAULT NULL,
  `n_cmu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nationalite` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `prenom_parent` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nom_parent` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telephone_parent` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `profession_pere` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicile_pere` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom_prenom_mere` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profession_mere` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone_mere` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicile_mere` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tuteur_legal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profession_tuteur` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone_tuteur` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicile_tuteur` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `statut_inscription` enum('En attente','Validé par étudiant','Validé définitivement') COLLATE utf8mb4_general_ci DEFAULT 'En attente',
  PRIMARY KEY (`id_etudiants`),
  KEY `fk_etudiants_filiere` (`id_filiere`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id_etudiants`, `id_permanent`, `prenom`, `nom`, `niveau`, `id_filiere`, `etablissement_origine`, `niveau_etude`, `cours`, `affecte`, `redoublant`, `diplome`, `serie_diplome`, `points_diplome`, `annee_diplome`, `n_matricule_bac`, `n_table_bac`, `asthme`, `hypertension`, `problemes_psychiques`, `handicap_physique`, `autres_infos_sante`, `nom_prenom_pere`, `telephone_pere`, `adresse`, `sexe`, `email`, `contact_etudiant`, `n_carte_identite`, `date_carte_identite`, `n_cmu`, `date_naissance`, `lieu_naissance`, `nationalite`, `date_inscription`, `prenom_parent`, `nom_parent`, `telephone_parent`, `profession_pere`, `domicile_pere`, `nom_prenom_mere`, `profession_mere`, `telephone_mere`, `domicile_mere`, `tuteur_legal`, `profession_tuteur`, `telephone_tuteur`, `domicile_tuteur`, `statut_inscription`) VALUES
(32, 'ASSB3008040001', 'barthelemy jean christ', 'assahoua', 'L1', 1, 'cpca', 'Tle', 'Jour', 'Non', 'Non', 'BAC', 'D', 300, '2025', '22222222f', '5555555', 'Non', 'Non', 'Non', 'Non', '', 'marc elie assahoua', '0749349430', '00225', 'Masculi', 'jeanchristassahoua7@gmail.com', '0703713891', '1111111111111', '2025-01-01', '555555', '2008-05-20', 'yopougon', 'ivoirienne', '2026-06-11 03:27:46', '', '', '', 'Policier', 'a', 'barthelemy jean christ assahoua', 'f', '0703713891', 'a', 'marc elie assahoua', 'Nean', '0749349430', 'Nean', 'Validé définitivement'),
(33, 'ASSB3008040001', 'barthelemy jean christ', 'assahoua', 'L1', 1, 'cpca', 'Tle', 'Jour', 'Non', 'Non', 'BAC', 'D', 300, '2025', '22222222f', '5555555', 'Non', 'Non', 'Non', 'Non', '', 'marc elie assahoua', '0749349430', '00225', 'Masculi', 'jeanchristassahoua7@gmail.com', '0703713891', '1111111111111', '2025-01-01', '555555', '2008-05-20', 'yopougon', 'ivoirienne', '2026-06-11 03:32:42', '', '', '', 'Policier', 'a', 'barthelemy jean christ assahoua', 'f', '0703713891', 'a', 'marc elie assahoua', 'Nean', '0749349430', 'Nean', 'Validé définitivement');

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

DROP TABLE IF EXISTS `filiere`;
CREATE TABLE IF NOT EXISTS `filiere` (
  `id_filiere` int NOT NULL AUTO_INCREMENT,
  `nom_filiere` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `abreviation_filiere` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `niveau` int NOT NULL,
  `id_departement` int DEFAULT NULL,
  PRIMARY KEY (`id_filiere`),
  KEY `fk_filiere_departement` (`id_departement`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filiere`
--

INSERT INTO `filiere` (`id_filiere`, `nom_filiere`, `abreviation_filiere`, `niveau`, `id_departement`) VALUES
(1, 'Informatique', 'IGL', 5, 1),
(15, 'Droit', 'Droit', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

DROP TABLE IF EXISTS `inscription`;
CREATE TABLE IF NOT EXISTS `inscription` (
  `id_inscription` int NOT NULL AUTO_INCREMENT,
  `id_etudiants` int NOT NULL,
  `id_filiere` int NOT NULL,
  `annee_academique` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_operation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `statut_inscription` enum('En attente','Validée','Annulée') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'En attente',
  PRIMARY KEY (`id_inscription`),
  KEY `fk_inscription_etudiant` (`id_etudiants`),
  KEY `fk_inscription_filiere` (`id_filiere`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `inscription`
--

INSERT INTO `inscription` (`id_inscription`, `id_etudiants`, `id_filiere`, `annee_academique`, `date_operation`, `statut_inscription`) VALUES
(20, 32, 1, '2026-2027', '2026-06-11 03:27:46', 'En attente'),
(21, 33, 1, '2026-2027', '2026-06-11 03:32:42', 'En attente');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id_notification` int NOT NULL AUTO_INCREMENT,
  `destinataire_role` enum('Admin','Responsable') NOT NULL,
  `id_destinataire_specifique` int DEFAULT NULL,
  `message` text NOT NULL,
  `statut_lecture` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notification`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id_notification`, `destinataire_role`, `id_destinataire_specifique`, `message`, `statut_lecture`, `created_at`) VALUES
(1, 'Responsable', 5, 'Nouvelle inscription à vérifier : L\'étudiant(e) barthelemy jean christ assahoua s\'est inscrit en Informatique (ID: 31) et attend votre validation.', 2, '2026-06-11 03:06:34'),
(2, 'Admin', NULL, 'L\'étudiant(e) barthelemy jean christ assahoua a soumis son dossier d\'inscription en ligne (ID: 31). En attente de validation.', 0, '2026-06-11 03:06:34'),
(3, 'Admin', NULL, 'Le responsable marc elie assahoua a validé définitivement l\'inscription de l\'étudiant : ASSAHOUA barthelemy jean christ (ID: 31).', 0, '2026-06-11 03:15:29'),
(4, 'Responsable', 5, 'Nouvelle inscription à vérifier : L\'étudiant(e) barthelemy jean christ assahoua s\'est inscrit en Informatique (ID: 32) et attend votre validation.', 2, '2026-06-11 03:27:46'),
(5, 'Admin', NULL, 'L\'étudiant(e) barthelemy jean christ assahoua a soumis son dossier d\'inscription en ligne (ID: 32). En attente de validation.', 0, '2026-06-11 03:27:46'),
(6, 'Responsable', 5, 'Nouvelle inscription à vérifier : L\'étudiant(e) barthelemy jean christ assahoua s\'est inscrit en Informatique (ID: 33) et attend votre validation.', 2, '2026-06-11 03:32:42'),
(7, 'Admin', NULL, 'L\'étudiant(e) barthelemy jean christ assahoua a soumis son dossier d\'inscription en ligne (ID: 33). En attente de validation.', 1, '2026-06-11 03:32:42'),
(8, 'Admin', NULL, 'Le responsable marc elie assahoua a validé définitivement l\'inscription de l\'étudiant : ASSAHOUA barthelemy jean christ (ID: 33).', 1, '2026-06-11 03:34:50'),
(9, 'Admin', NULL, 'Le responsable marc elie assahoua a validé définitivement l\'inscription de l\'étudiant : ASSAHOUA barthelemy jean christ (ID: 32).', 1, '2026-06-11 03:34:54'),
(10, 'Responsable', 5, 'Nouvelle inscription à vérifier : L\'étudiant(e) barthelemy jean christ assahoua s\'est inscrit en Informatique (ID: 34) et attend votre validation.', 0, '2026-06-11 03:40:13'),
(11, 'Admin', NULL, 'L\'étudiant(e) barthelemy jean christ assahoua a soumis son dossier d\'inscription en ligne (ID: 34). En attente de validation.', 1, '2026-06-11 03:40:13');

-- --------------------------------------------------------

--
-- Structure de la table `responsable_departement`
--

DROP TABLE IF EXISTS `responsable_departement`;
CREATE TABLE IF NOT EXISTS `responsable_departement` (
  `id_responsable` int NOT NULL AUTO_INCREMENT,
  `nomutilisateur` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `motdepasse` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_responsable`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `responsable_departement`
--

INSERT INTO `responsable_departement` (`id_responsable`, `nomutilisateur`, `motdepasse`, `nom`, `prenom`, `telephone`, `email`, `reset_token`) VALUES
(5, 'jean', '$2y$10$M3jneJpAd5NxDeJrSuLMv.l787BJzhM37i2xZi0xOn.O6EmPspg02', 'assahoua', 'marc elie', '0101010101', 'jeanchris@gmail.com', NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `departement`
--
ALTER TABLE `departement`
  ADD CONSTRAINT `fk_departement_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `responsable_departement` (`id_responsable`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `fk_etudiants_filiere` FOREIGN KEY (`id_filiere`) REFERENCES `filiere` (`id_filiere`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD CONSTRAINT `fk_filiere_departement` FOREIGN KEY (`id_departement`) REFERENCES `departement` (`id_departement`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `fk_inscription_etudiant` FOREIGN KEY (`id_etudiants`) REFERENCES `etudiants` (`id_etudiants`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inscription_filiere` FOREIGN KEY (`id_filiere`) REFERENCES `filiere` (`id_filiere`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
