-- GIRH - Gestion des Intérimaires RH
-- Schéma mis à jour (v2) — sans table missions

CREATE DATABASE IF NOT EXISTS girh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE girh;

DROP TABLE IF EXISTS feuilles_temps;
DROP TABLE IF EXISTS missions;
DROP TABLE IF EXISTS interimaires;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'agent') NOT NULL DEFAULT 'agent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_entreprise VARCHAR(200) NOT NULL,
    secteur_activite VARCHAR(100) NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(150),
    contact_principal VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS interimaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    cin VARCHAR(20) NOT NULL UNIQUE,
    fonction VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(150),
    adresse VARCHAR(255),
    date_naissance DATE,
    entreprise_id INT,
    type_contrat ENUM('CDI','CDD','ANAPEC') NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    salaire DECIMAL(10,2) NOT NULL,
    type_salaire ENUM('horaire','mensuel') NOT NULL DEFAULT 'mensuel',
    mode_paiement ENUM('espece','virement','cheque') NOT NULL DEFAULT 'virement',
    statut ENUM('paie_bloquee','en_mission','fin_de_contrat') NOT NULL DEFAULT 'en_mission',
    competences TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_interimaire_entreprise FOREIGN KEY (entreprise_id) REFERENCES entreprises(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS feuilles_temps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    interimaire_id INT NOT NULL,
    date DATE NOT NULL,
    heures_travaillees DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feuille_interimaire FOREIGN KEY (interimaire_id) REFERENCES interimaires(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Données de test
-- Mot de passe admin : admin123 | agent : agent123

INSERT INTO utilisateurs (nom, email, mot_de_passe_hash, role) VALUES
('Administrateur GIRH', 'admin@girh.ma', '$2y$10$F1GTbGBke8k5OIyXLB0hv.Ct.UEZNwZbnIZSQEYkWANx5R4Gpi9Ku', 'admin'),
('Fatima Zahra', 'agent@girh.ma', '$2y$10$.w2SxVl5tB8OTGyFJpPj3u810TtWzzWYmWfEhah4Bm12YoPABMsVu', 'agent');

INSERT INTO entreprises (nom_entreprise, secteur_activite, adresse, telephone, email, contact_principal) VALUES
('Maroc Logistique SA', 'Logistique', 'Zone industrielle, Casablanca', '0522123456', 'contact@maroclog.ma', 'Hassan Berrada'),
('Tech Solutions Maroc', 'Informatique', 'Boulevard Zerktouni, Casablanca', '0522987654', 'info@techsolutions.ma', 'Amine Tazi'),
('AgroFood Industries', 'Agroalimentaire', 'Route de Rabat, Kénitra', '0537123456', 'rh@agrofood.ma', 'Samira El Amrani'),
('Atlas Construction', 'BTP', 'Hay Riad, Rabat', '0537654321', 'contact@atlas-btp.ma', 'Mohamed Idrissi'),
('MediCare Clinics', 'Santé', 'Quartier Hassan, Rabat', '0537889900', 'admin@medicare.ma', 'Dr. Karim Bennani');

INSERT INTO interimaires (nom, prenom, cin, fonction, telephone, email, adresse, date_naissance, entreprise_id, type_contrat, date_debut, date_fin, salaire, type_salaire, mode_paiement, statut, competences) VALUES
('Alami', 'Youssef', 'AB123456', 'Manutentionnaire', '0612345678', 'y.alami@email.ma', 'Casablanca, Hay Mohammadi', '1995-03-15', 1, 'CDD', '2025-12-01', '2026-07-10', 45.00, 'horaire', 'virement', 'en_mission', 'Manutention, Logistique'),
('Benjelloun', 'Sara', 'CD789012', 'Assistante RH', '0623456789', 's.benjelloun@email.ma', 'Rabat, Agdal', '1998-07-22', 2, 'CDI', '2024-06-01', NULL, 8500.00, 'mensuel', 'virement', 'en_mission', 'Secrétariat, Recrutement'),
('Chraibi', 'Karim', 'EF345678', 'Comptable', '0634567890', 'k.chraibi@email.ma', 'Marrakech, Gueliz', '1992-11-08', 2, 'CDD', '2025-09-15', '2026-03-15', 55.00, 'horaire', 'cheque', 'fin_de_contrat', 'Comptabilité, Excel'),
('Daoudi', 'Nadia', 'GH901234', 'Community Manager', '0645678901', 'n.daoudi@email.ma', 'Tanger, Malabata', '2000-01-30', 3, 'ANAPEC', '2026-01-10', '2026-07-05', 6000.00, 'mensuel', 'virement', 'en_mission', 'Marketing, Réseaux sociaux'),
('El Fassi', 'Omar', 'IJ567890', 'Contrôleur qualité', '0656789012', 'o.elfassi@email.ma', 'Fès, Ville Nouvelle', '1990-09-18', 4, 'CDD', '2025-11-01', '2026-05-01', 50.00, 'horaire', 'espece', 'paie_bloquee', 'Production, Qualité'),
('Ghazali', 'Leila', 'KL234567', 'Agent d\'accueil', '0667890123', 'l.ghazali@email.ma', 'Casablanca, Maarif', '1997-05-25', 5, 'CDD', '2026-02-01', '2026-08-01', 4800.00, 'mensuel', 'virement', 'en_mission', 'Accueil, Administration');

INSERT INTO feuilles_temps (interimaire_id, date, heures_travaillees) VALUES
(1, '2026-05-01', 160.00),
(1, '2026-06-01', 152.00),
(3, '2026-05-01', 168.00),
(5, '2026-05-01', 176.00),
(5, '2026-06-01', 160.00);
