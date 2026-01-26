-- Create database if not exists
CREATE DATABASE IF NOT EXISTS job_dating_youcode;
USE job_dating_youcode;

-- Users table: stores all users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','apprenant') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Admins table: each admin linked to a user
-- ON DELETE CASCADE ensures deleting a user deletes the admin record
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Apprenants table: each learner linked to a user
-- ON DELETE CASCADE ensures deleting a user deletes the apprenant record
CREATE TABLE apprenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    promotion VARCHAR(50),
    specialisation VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Entreprises table: stores companies
CREATE TABLE entreprises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    secteur VARCHAR(100),
    localisation VARCHAR(150),
    image VARCHAR(255),
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(30)
);

-- Annonces table: job ads linked to entreprises
-- ON DELETE CASCADE ensures deleting a company deletes its annonces automatically
CREATE TABLE annonces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    entreprise_id INT NOT NULL,
    description TEXT,
    type_contrat VARCHAR(50),
    localisation VARCHAR(150),
    competences TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deleted_at BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (entreprise_id) REFERENCES entreprises(id) ON DELETE CASCADE
);

-- Applications table: apprenant job applications
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    announcement_id INT NOT NULL,
    motivation TEXT NOT NULL,
    cv_path VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (announcement_id) REFERENCES annonces(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (student_id, announcement_id)
);
