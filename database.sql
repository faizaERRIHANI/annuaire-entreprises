CREATE DATABASE IF NOT EXISTS annuaire_entreprises
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE annuaire_entreprises;

CREATE TABLE IF NOT EXISTS entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    categorie VARCHAR(100),
    adresse TEXT,
    telephone VARCHAR(20),
    email VARCHAR(150),
    site_web VARCHAR(200),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    description TEXT,
    logo VARCHAR(255),
    note_moyenne DECIMAL(2,1) DEFAULT 0,
    nombre_avis INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entreprise_id INT NOT NULL,
    auteur VARCHAR(100) NOT NULL,
    note INT NOT NULL,
    commentaire TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_note CHECK (note >= 1 AND note <= 5),
    CONSTRAINT fk_avis_entreprise
        FOREIGN KEY (entreprise_id)
        REFERENCES entreprises(id)
        ON DELETE CASCADE
);