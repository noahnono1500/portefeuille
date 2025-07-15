-- Création de la base et de l'utilisateur
DROP DATABASE IF EXISTS portfolio;
CREATE DATABASE portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE portfolio;

-- Création de l'utilisateur MySQL (à exécuter si droits suffisants)
CREATE USER IF NOT EXISTS 'portfolio'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON portfolio.* TO 'portfolio'@'localhost';
FLUSH PRIVILEGES;

-- Table users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table skills
CREATE TABLE skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Table user_skills
CREATE TABLE user_skills (
    user_id INT,
    skill_id INT,
    level ENUM('débutant', 'intermédiaire', 'avancé', 'expert'),
    PRIMARY KEY(user_id, skill_id),
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(skill_id) REFERENCES skills(id) ON DELETE CASCADE
);

-- Table projects
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255),
    description TEXT,
    image VARCHAR(255),
    external_link VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion des utilisateurs
INSERT INTO users (email, password, fullname, role) VALUES
('admin@example.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ab', 'Admin User', 'admin'),
('user1@example.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ab', 'User One', 'user'),
('user2@example.com', '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ab', 'User Two', 'user');

-- Insertion de compétences
INSERT INTO skills (name) VALUES
('PHP'), ('JavaScript'), ('HTML'), ('CSS'), ('MySQL'), ('Python');

-- Attribution des compétences
INSERT INTO user_skills (user_id, skill_id, level) VALUES
(1, 1, 'expert'),
(2, 2, 'intermédiaire'),
(3, 3, 'débutant');

-- Insertion de projets
INSERT INTO projects (user_id, title, description, image, external_link) VALUES
(1, 'Admin Portfolio', 'Projet principal de l’admin', 'admin1.jpg', 'https://adminproject.com'),
(2, 'Projet User 1', 'Premier projet de l’utilisateur 1', 'user1_1.jpg', 'https://user1project.com'),
(2, 'Projet User 1 - 2', 'Deuxième projet de l’utilisateur 1', 'user1_2.jpg', 'https://user1project2.com'),
(3, 'Projet User 2', 'Projet unique de l’utilisateur 2', 'user2_1.jpg', 'https://user2project.com');
