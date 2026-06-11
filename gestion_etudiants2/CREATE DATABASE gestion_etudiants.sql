CREATE TABLE `responsablefiliere` (
  `id_responsablefiliere`       INT(11),         
  `nomutilisateur`     VARCHAR(127)    NOT NULL,
  `motdepasse`        VARCHAR(255)    NOT NULL,
  `filiere`              VARCHAR(31)     NOT NULL,          
  `nom`              VARCHAR(127)    NOT NULL,
  `prenom`                 VARCHAR(127)    NOT NULL,          
    `telephone`           VARCHAR(31)     NOT NULL,         
    `email`               VARCHAR(255)    NOT NULL
    `reset_token` VARCHAR(255) NULL;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `etudiants` (
  `id_etudiants`          INT(11) AUTO_INCREMENT PRIMARY KEY,
  `id_permanent`          VARCHAR(20), -- [cite: 10]
  `nom`                   VARCHAR(255) NOT NULL, -- [cite: 37]
  `prenom`                VARCHAR(127) NOT NULL, -- [cite: 37]
  `sexe`                  VARCHAR(7) NOT NULL, -- [cite: 38]
  `date_naissance`        DATE NOT NULL, -- [cite: 39]
  `lieu_naissance`        VARCHAR(100), -- [cite: 39]
  `nationalite`           VARCHAR(50), -- [cite: 11]
  `email`                 VARCHAR(255) NOT NULL, -- [cite: 46]
  `contact_etudiant`      VARCHAR(31), -- [cite: 45]
  `adresse`               VARCHAR(31) NOT NULL, -- [cite: 29]
  
  -- Informations Académiques
  `id_filiere`            VARCHAR(10) NOT NULL, -- [cite: 8]
  `niveau`            VARCHAR(30) NOT NULL,
  `etablissement_origine` VARCHAR(255), -- 
  `niveau_etude`          VARCHAR(50), -- [cite: 43]
  `cours`                 VARCHAR(20), -- [cite: 44]
  `affecte`               VARCHAR(10), -- [cite: 19]
  `redoublant`            VARCHAR(5), -- [cite: 20]
  
  -- Diplôme (BAC)
  `diplome`               VARCHAR(100), -- [cite: 14]
  `serie_diplome`         VARCHAR(10), -- [cite: 14]
  `points_diplome`        INT(5), -- [cite: 15]
  `annee_diplome`         YEAR, -- [cite: 15]
  `n_matricule_bac`       VARCHAR(50), -- 
  `n_table_bac`           VARCHAR(50), -- 
  `n_cmu`                 VARCHAR(50), -- 
  
  -- Identité
  `n_carte_identite`      VARCHAR(50), -- [cite: 12]
  `date_carte_identite`   DATE, -- Champ prévu mais vide sur la fiche [cite: 12]
  
  -- Santé
  `asthme`                VARCHAR(5), -- [cite: 22]
  `hypertension`          VARCHAR(5), -- [cite: 23]
  `problemes_psychiques`  VARCHAR(5), -- [cite: 24]
  `handicap_physique`     VARCHAR(5), -- [cite: 24]
  `autres_infos_sante`    TEXT, -- [cite: 47]
  
  -- Parents & Correspondance
  `nom_prenom_pere`       VARCHAR(255), -- [cite: 26]
  `profession_pere`       VARCHAR(100), -- [cite: 27]
  `telephone_pere`        VARCHAR(31), -- [cite: 29]
  `domicile_pere`         VARCHAR(100), -- [cite: 29]
  
  `nom_prenom_mere`       VARCHAR(255), -- [cite: 48]
  `profession_mere`       VARCHAR(100), -- [cite: 48]
  `telephone_mere`        VARCHAR(31), -- [cite: 50]
  `domicile_mere`         VARCHAR(100), -- [cite: 50]
  
  `tuteur_legal`          VARCHAR(255), -- [cite: 30]
  `profession_tuteur`     VARCHAR(100), -- [cite: 31]
  `telephone_tuteur`      VARCHAR(31), -- [cite: 34]
  `domicile_tuteur`       VARCHAR(100), -- [cite: 34]
  
  `date_inscription`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- [cite: 1]
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `administrateur` (
  `administrateur_id` int(11) NOT NULL,
  `nomutilisateur` varchar(127) NOT NULL,
  `motsdepasse` varchar(255) NOT NULL,
  `nom` varchar(127) NOT NULL,
  `prenom` varchar(127) NOT NULL
  `email` VARCHAR(255)
  `reset_token` VARCHAR(255) NULL;
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `administrateur` (`administrateur_id`, `nomUtilisateur`, `motsdepasse`, `nom`, `prenom`) VALUES
(1, 'chris', '$2y$10$elG8Q8MYheDfQoAWDqCHnuCrTvs7.BgEn7/tk.A3D.GDl4QO0pMXi', 'Chris', 'Assahoua');
CREATE TABLE `filiere` (
  `id_filiere`     INT(11)        NOT NULL,
  `nom_filiere`    VARCHAR(31)    NOT NULL,           -- ex: Mathématiques, Physique-Chimie
  `abreviation_filiere`   VARCHAR(31)    NOT NULL,           -- ex: MATHS, PC, SVT, ANG
  `niveau`         INT(11)        NOT NULL           -- ex: 6 pour 6ème, 1 pour 1ère, etc.
  
  -- Suggestion : ajouter un index si vous filtrez souvent par niveau
  -- INDEX idx_niveau (niveau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`administrateur_id`),
  ADD UNIQUE KEY `nomutilisateur` (`nomutilisateur`);
ADD UNIQUE (`email`);
-- Indexes for table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id_etudiants`);

-- Indexes for table `responsablefiliere`
--
ALTER TABLE `responsablefiliere`
  ADD PRIMARY KEY (`id_responsablefiliere`),
  ADD UNIQUE KEY `nomutilisateur` (`nomutilisateur`);

-- Indexes for table `filiere`
--
ALTER TABLE `filiere`
  ADD PRIMARY KEY (`id_filiere`);
  
-- AUTO_INCREMENT for table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `administrateur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- AUTO_INCREMENT for table `filiere`
--
ALTER TABLE `filiere`
  MODIFY `id_filiere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- AUTO_INCREMENT for table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id_etudiants` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- AUTO_INCREMENT for table `responsablefiliere`
--
ALTER TABLE `responsablefiliere`
  MODIFY `id_responsablefiliere` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
  CREATE TABLE `inscription` (
  `id_inscription` INT(11) NOT NULL AUTO_INCREMENT,
  `id_etudiants` INT(11) NOT NULL,
  `id_filiere` INT(11) NOT NULL,
  `annee_academique` VARCHAR(9) NOT NULL, -- ex: 2025-2026
  `date_operation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `statut_inscription` ENUM('En attente', 'Validée', 'Annulée') DEFAULT 'En attente',
  PRIMARY KEY (`id_inscription`),
  -- Contraintes d'intégrité pour lier aux autres tables
  CONSTRAINT `fk_inscription_etudiant` FOREIGN KEY (`id_etudiants`) REFERENCES `etudiants`(`id_etudiants`) ON DELETE CASCADE,
  CONSTRAINT `fk_inscription_filiere` FOREIGN KEY (`id_filiere`) REFERENCES `filiere`(`id_filiere`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;