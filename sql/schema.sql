-- GIRH - Gestion des Intérimaires RH
-- Script de création de la base de données

CREATE DATABASE IF NOT EXISTS girh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE girh;

-- Table des utilisateurs (authentification)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'agent') NOT NULL DEFAULT 'agent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des intérimaires
CREATE TABLE IF NOT EXISTS interimaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    cin VARCHAR(20) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    email VARCHAR(150),
    adresse TEXT,
    date_naissance DATE,
    competences TEXT,
    disponibilite ENUM('disponible', 'en_mission', 'indisponible') NOT NULL DEFAULT 'disponible',
    date_inscription DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des entreprises clientes
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

-- Table des missions
CREATE TABLE IF NOT EXISTS missions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise_id INT NOT NULL,
    interimaire_id INT NOT NULL,
    poste VARCHAR(150) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    salaire_horaire DECIMAL(10, 2) NOT NULL,
    statut ENUM('en_cours', 'terminee', 'renouvelee') NOT NULL DEFAULT 'en_cours',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mission_entreprise FOREIGN KEY (entreprise_id) REFERENCES entreprises(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_mission_interimaire FOREIGN KEY (interimaire_id) REFERENCES interimaires(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Table des feuilles de temps (bonus)
CREATE TABLE IF NOT EXISTS feuilles_temps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mission_id INT NOT NULL,
    date DATE NOT NULL,
    heures_travaillees DECIMAL(5, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feuille_mission FOREIGN KEY (mission_id) REFERENCES missions(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Données de test
-- Mot de passe admin : admin123 | agent : agent123
-- =====================================================

INSERT INTO utilisateurs (nom, email, mot_de_passe_hash, role) VALUES
('Administrateur GIRH', 'admin@girh.ma', '$2y$10$F1GTbGBke8k5OIyXLB0hv.Ct.UEZNwZbnIZSQEYkWANx5R4Gpi9Ku', 'admin'),
('Fatima Zahra', 'agent@girh.ma', '$2y$10$.w2SxVl5tB8OTGyFJpPj3u810TtWzzWYmWfEhah4Bm12YoPABMsVu', 'agent');

INSERT INTO interimaires (nom, prenom, cin, telephone, email, adresse, date_naissance, competences, disponibilite, date_inscription) VALUES
('Alami', 'Youssef', 'AB123456', '0612345678', 'y.alami@email.ma', 'Casablanca, Hay Mohammadi', '1995-03-15', 'Manutention, Logistique', 'en_mission', '2025-01-10'),
('Benjelloun', 'Sara', 'CD789012', '0623456789', 's.benjelloun@email.ma', 'Rabat, Agdal', '1998-07-22', 'Secrétariat, Accueil', 'disponible', '2025-02-05'),
('Chraibi', 'Karim', 'EF345678', '0634567890', 'k.chraibi@email.ma', 'Marrakech, Gueliz', '1992-11-08', 'Comptabilité, Excel', 'en_mission', '2024-11-20'),
('Daoudi', 'Nadia', 'GH901234', '0645678901', 'n.daoudi@email.ma', 'Tanger, Malabata', '2000-01-30', 'Marketing, Réseaux sociaux', 'disponible', '2025-03-12'),
('El Fassi', 'Omar', 'IJ567890', '0656789012', 'o.elfassi@email.ma', 'Fès, Ville Nouvelle', '1990-09-18', 'Production, Qualité', 'indisponible', '2024-08-01'),
('Ghazali', 'Leila', 'KL234567', '0667890123', 'l.ghazali@email.ma', 'Casablanca, Maarif', '1997-05-25', 'RH, Recrutement', 'disponible', '2025-04-01');

INSERT INTO entreprises (nom_entreprise, secteur_activite, adresse, telephone, email, contact_principal) VALUES
('Maroc Logistique SA', 'Logistique', 'Zone industrielle, Casablanca', '0522123456', 'contact@maroclog.ma', 'Hassan Berrada'),
('Tech Solutions Maroc', 'Informatique', 'Boulevard Zerktouni, Casablanca', '0522987654', 'info@techsolutions.ma', 'Amine Tazi'),
('AgroFood Industries', 'Agroalimentaire', 'Route de Rabat, Kénitra', '0537123456', 'rh@agrofood.ma', 'Samira El Amrani'),
('Atlas Construction', 'BTP', 'Hay Riad, Rabat', '0537654321', 'contact@atlas-btp.ma', 'Mohamed Idrissi'),
('MediCare Clinics', 'Santé', 'Quartier Hassan, Rabat', '0537889900', 'admin@medicare.ma', 'Dr. Karim Bennani');

INSERT INTO missions (entreprise_id, interimaire_id, poste, date_debut, date_fin, salaire_horaire, statut) VALUES
(1, 1, 'Manutentionnaire', '2026-04-01', '2026-06-20', 45.00, 'en_cours'),
(2, 3, 'Assistant comptable', '2026-03-15', '2026-06-22', 55.00, 'en_cours'),
(3, 1, 'Opérateur de production', '2026-01-10', '2026-05-30', 40.00, 'terminee'),
(4, 3, 'Contrôleur qualité', '2026-02-01', '2026-06-18', 50.00, 'renouvelee'),
(5, 1, 'Agent d\'accueil médical', '2025-12-01', '2026-03-01', 48.00, 'terminee');

INSERT INTO feuilles_temps (mission_id, date, heures_travaillees) VALUES
(1, '2026-06-01', 8.00),
(1, '2026-06-02', 7.50),
(1, '2026-06-03', 8.00),
(2, '2026-06-01', 8.00),
(2, '2026-06-02', 8.00),
(4, '2026-06-01', 6.00),
(4, '2026-06-02', 8.00);
