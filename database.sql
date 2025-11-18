-- Base de données pour le module Formation et Apprentissage
-- Créer la base de données
CREATE DATABASE IF NOT EXISTS formation_apprentissage;
USE formation_apprentissage;

-- Table Cours
CREATE TABLE IF NOT EXISTS cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    duree INT NOT NULL COMMENT 'Durée en heures',
    niveau VARCHAR(50) NOT NULL COMMENT 'Débutant, Intermédiaire, Avancé, Expert',
    formateur VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table Chapitre (Relation 1-N avec Cours : un cours contient plusieurs chapitres)
CREATE TABLE IF NOT EXISTS chapitre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    ordre INT NOT NULL COMMENT 'Ordre du chapitre dans le cours',
    cours_id INT NOT NULL,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertion de quelques données d'exemple
INSERT INTO cours (titre, description, duree, niveau, formateur) VALUES
('PHP Avancé', 'Formation approfondie sur les concepts avancés de PHP', 40, 'Avancé', 'Ahmed Ben Ali'),
('JavaScript pour Débutants', 'Introduction au langage JavaScript', 30, 'Débutant', 'Sara Trabelsi'),
('Base de Données MySQL', 'Formation complète sur MySQL', 35, 'Intermédiaire', 'Mohamed Khelifi');

-- Chapitres pour le cours PHP Avancé (id=1)
INSERT INTO chapitre (titre, description, ordre, cours_id) VALUES
('Introduction à PHP Avancé', 'Vue d\'ensemble des concepts avancés', 1, 1),
('POO en PHP', 'Programmation Orientée Objet avec PHP', 2, 1),
('Design Patterns', 'Les patterns de conception en PHP', 3, 1);

-- Chapitres pour le cours JavaScript (id=2)
INSERT INTO chapitre (titre, description, ordre, cours_id) VALUES
('Les bases de JavaScript', 'Variables, types de données, opérateurs', 1, 2),
('Fonctions et structures de contrôle', 'Apprendre à créer des fonctions', 2, 2),
('Manipulation du DOM', 'Interagir avec les éléments HTML', 3, 2);

-- Chapitres pour le cours MySQL (id=3)
INSERT INTO chapitre (titre, description, ordre, cours_id) VALUES
('Introduction à MySQL', 'Présentation de MySQL et installation', 1, 3),
('Requêtes SQL de base', 'SELECT, INSERT, UPDATE, DELETE', 2, 3),
('Relations et jointures', 'Gérer les relations entre tables', 3, 3);
