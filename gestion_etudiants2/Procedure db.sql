-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_etudiants`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

DROP TABLE IF EXISTS `administrateur`;
CREATE TABLE IF NOT EXISTS `administrateur` (
  `administrateur_id` int NOT NULL AUTO_INCREMENT,
  `nomutilisateur` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `motsdepasse` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`administrateur_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `administrateur` (`administrateur_id`, `nomutilisateur`, `motsdepasse`, `nom`, `email`, `prenom`, `reset_token`) VALUES
(1, 'chris', '$2y$10$elG8Q8MYheDfQoAWDqCHnuCrTvs7.BgEn7/tk.A3D.GDl4QO0pMXi', 'Chris', 'jeanchristassahoua7@gmail.com', 'Assahoua', '1092d61cc4b1a38e9d7dc48b9d7db5d6'),
(2, 'A', '$2y$10$oT8k1l/ZxDWJgO0I4mqt1eyKz215c.MGY5sHVnAtlDI0G2VvLMVJG', 'A', 'A@gmail.com', 'A', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `responsable_departement`
--

DROP TABLE IF EXISTS `responsable_departement`;
CREATE TABLE IF NOT EXISTS `responsable_departement` (
  `id_responsable` int NOT NULL AUTO_INCREMENT,
  `nomutilisateur` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `motdepasse` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(31) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_responsable`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Migration de ton ancien responsable de filière
INSERT INTO `responsable_departement` (`id_responsable`, `nomutilisateur`, `motdepasse`, `nom`, `prenom`, `telephone`, `email`, `reset_token`) VALUES
(1, 'jean', '$2y$10$i5Fkfhntmi32EWqteANaMuLgNMEv9uX.aq0y9P4Axhz.1/fZ5U5hu', 'assahoua', 'barthelemy jean christ', '0703713891', 'jeanchristassahoua7@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `id_departement` int NOT NULL AUTO_INCREMENT,
  `nom_departement` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `id_responsable` int DEFAULT NULL,
  PRIMARY KEY (`id_departement`),
  KEY `fk_departement_responsable` (`id_responsable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création des départements par défaut et attribution du responsable 1 aux STGI (Informatique)
INSERT INTO `departement` (`id_departement`, `nom_departement`, `id_responsable`) VALUES
(1, 'Sciences Technologies de l\'Information et Génie Informatique', 1),
(2, 'Sciences Économiques et de Gestion', NULL),
(3, 'Sciences Juridiques et Politiques', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

DROP TABLE IF EXISTS `filiere`;
CREATE TABLE IF NOT EXISTS `filiere` (
  `id_filiere` int NOT NULL AUTO_INCREMENT,
  `nom_filiere` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `abreviation_filiere` varchar(31) COLLATE utf8mb4_general_ci NOT NULL,
  `id_departement` int DEFAULT NULL,
  PRIMARY KEY (`id_filiere`),
  KEY `fk_filiere_departement` (`id_departement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertion de tes filières reliées à leurs départements respectifs
INSERT INTO `filiere` (`id_filiere`, `nom_filiere`, `abreviation_filiere`, `id_departement`) VALUES
(1, 'Informatique', 'IGL', 1),
(12, 'Informatique Option Génie Logiciel', 'IGL', 1),
(14, 'Science Economie de Gestion', 'SEG', 2),
(15, 'Droit', 'Droit', 3);

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

DROP TABLE IF EXISTS `etudiants`;
CREATE TABLE IF NOT EXISTS `etudiants` (
  `id_etudiants` int NOT NULL AUTO_INCREMENT,
  `id_permanent` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prenom` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `niveau` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `id_filiere` int NOT NULL,
  `etablissement_origine` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `niveau_etude` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cours` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `affecte` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `redoublant` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diplome` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serie_diplome` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `points_diplome` int DEFAULT NULL,
  `annee_diplome` year DEFAULT NULL,
  `n_matricule_bac` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `n_table_bac` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asthme` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hypertension` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `problemes_psychiques` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `handicap_physique` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `autres_infos_sante` text COLLATE utf8mb4_general_ci,
  `nom_prenom_pere` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone_pere` varchar(31) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adresse` varchar(31) COLLATE utf8mb4_general_ci NOT NULL,
  `sexe` varchar(7) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_etudiant` varchar(31) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `n_carte_identite` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_carte_identite` date DEFAULT NULL,
  `n_cmu` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_naissance` date NOT NULL,
  `lieu_naissance` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nationalite` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `prenom_parent` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `nom_parent` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone_parent` varchar(31) COLLATE utf8mb4_general_ci NOT NULL,
  `profession_pere` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicile_pere` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nom_prenom_mere` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profession_mere` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone_mere` varchar(31) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicile_mere` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tuteur_legal` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profession_tuteur` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telephone_tuteur` varchar(31) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domicile_tuteur` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_etudiants`),
  KEY `fk_etudiants_filiere` (`id_filiere`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `etudiants` (`id_etudiants`, `id_permanent`, `prenom`, `nom`, `niveau`, `id_filiere`, `etablissement_origine`, `niveau_etude`, `cours`, `affecte`, `redoublant`, `diplome`, `serie_diplome`, `points_diplome`, `annee_diplome`, `n_matricule_bac`, `n_table_bac`, `asthme`, `hypertension`, `problemes_psychiques`, `handicap_physique`, `autres_infos_sante`, `nom_prenom_pere`, `telephone_pere`, `adresse`, `sexe`, `email`, `contact_etudiant`, `n_carte_identite`, `date_carte_identite`, `n_cmu`, `date_naissance`, `lieu_naissance`, `nationalite`, `date_inscription`, `prenom_parent`, `nom_parent`, `telephone_parent`, `profession_pere`, `domicile_pere`, `nom_prenom_mere`, `profession_mere`, `telephone_mere`, `domicile_mere`, `tuteur_legal`, `profession_tuteur`, `telephone_tuteur`, `domicile_tuteur`) VALUES
(28, 'ASSB3008040002', 'barthelemy jean christ', 'assahoua', 'L1', 1, 'cpca', 'Tle', 'Jour', 'Oui', 'Non', 'BAC', 'D', 301, '2025', '22222222f', '5555555', 'Non', 'Non', 'Non', 'Non', '', 'marc elie assahoua', '0749349430', '00225', 'Masculi', 'jeanchristassahoua7@gmail.com', '0703713891', '1111111111111', '2025-05-06', '555555', '2000-02-02', 'yopougon', 'ivoirienne', '2026-03-22 16:02:09', '', '', '', 'f', 'a', 'marc elie assahoua', 'f', '0749349430', 'a', 'marc elie assahoua', 'f', '0749349430', 'a'),
(29, 'ASSB3008040002', 'barthelemy jean christ', 'assahoua', 'L1', 1, 'cpca', 'Tle', 'Jour', 'Oui', 'Non', 'BAC', 'D', 350, '2025', '22222222f', '5555555', 'Non', 'Non', 'Non', 'Non', '', 'marc elie assahoua', '0749349430', '00225', 'Masculi', 'jeanchristassahoua@gmail.com', '0703713891', '1111111111111', '2025-06-05', '555555', '2005-02-20', 'yopougon', 'ivoirienne', '2026-03-25 22:50:15', '', '', '', 'f', 'a', 'marc elie assahoua', 'f', '0749349430', 'a', 'marc elie assahoua', 'f', '0749349430', 'a'),
(30, 'ASSB300804000', 'êve-roxanne', 'Ouattara', 'L1', 1, 'Lycée moderne d\'abingourou', 'Tle', 'Jour', 'Oui', 'Non', 'BAC', 'D', 300, '2025', '111111111', '1111111111', 'Non', 'Non', 'Non', 'Non', '', 'Ouattara ambroise', '0101273420', 'Sopim', 'Féminin', 'roxanne@gmail.com', '0143401390', '444444444', '2025-05-02', '888888888', '2008-05-02', 'Abingorou', 'ivoirienne', '2026-04-14 17:01:05', '', '', '', 'Policier', 'Abingourou', 'Ouattara Alimata', 'Enseignante', '0101010101', 'Abingourou', 'Nean', 'Nean', 'Nean', 'Nean');

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

DROP TABLE IF EXISTS `inscription`;
CREATE TABLE IF NOT EXISTS `inscription` (
  `id_inscription` int NOT NULL AUTO_INCREMENT,
  `id_etudiants` int NOT NULL,
  `id_filiere` int NOT NULL,
  `annee_academique` varchar(9) COLLATE utf8mb4_general_ci NOT NULL,
  `date_operation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `statut_inscription` enum('En attente','Validée','Annulée') COLLATE utf8mb4_general_ci DEFAULT 'En attente',
  PRIMARY KEY (`id_inscription`),
  KEY `fk_inscription_etudiant` (`id_etudiants`),
  KEY `fk_inscription_filiere` (`id_filiere`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `inscription` (`id_inscription`, `id_etudiants`, `id_filiere`, `annee_academique`, `date_operation`, `statut_inscription`) VALUES
(16, 28, 1, '2026-2027', '2026-03-22 16:02:09', 'Validée'),
(17, 29, 1, '2026-2027', '2026-03-25 22:50:15', 'Validée'),
(18, 30, 1, '2026-2027', '2026-04-14 17:01:05', 'Validée');

--
-- Contraintes pour les tables déchargées
--

ALTER TABLE `departement`
  ADD CONSTRAINT `fk_departement_responsable` FOREIGN KEY (`id_responsable`) REFERENCES `responsable_departement` (`id_responsable`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `filiere`
  ADD CONSTRAINT `fk_filiere_departement` FOREIGN KEY (`id_departement`) REFERENCES `departement` (`id_departement`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `etudiants`
  ADD CONSTRAINT `fk_etudiants_filiere` FOREIGN KEY (`id_filiere`) REFERENCES `filiere` (`id_filiere`) ON UPDATE CASCADE;

ALTER TABLE `inscription`
  ADD CONSTRAINT `fk_inscription_etudiant` FOREIGN KEY (`id_etudiants`) REFERENCES `etudiants` (`id_etudiants`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inscription_filiere` FOREIGN KEY (`id_filiere`) REFERENCES `filiere` (`id_filiere`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;