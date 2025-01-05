<?php

function getDatabaseConfigFromControleur() {
    $jsonContent = file_get_contents('./databaseConfig.json');
    return json_decode($jsonContent, true);
}

function getDatabaseConfig() {
    $jsonContent = file_get_contents('../controleur/databaseConfig.json');
    return json_decode($jsonContent, true);
}

function saveDatabaseConfig($config) {
    $jsonContent = json_encode($config, JSON_PRETTY_PRINT);
    file_put_contents('./databaseConfig.json', $jsonContent);
}

function connectToDatabase() {
    $config = getDatabaseConfig();
    $pdo = new PDO(
        "mysql:host=" . $config['serveur'] . ";dbname=" . $config['bdd'],
        $config['utilisateur'],
        $config['mdp']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
function connectToDatabaseParams($serveur, $bdd, $utilisateur, $mdp) {
    $pdo = new PDO(
        "mysql:host=" . $serveur . ";dbname=" . $bdd,
        $utilisateur,
        $mdp
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function connectToDatabaseFromControleur() {
    $config = getDatabaseConfigFromControleur();
    $pdo = new PDO(
        "mysql:host=" . $config['serveur'] . ";dbname=" . $config['bdd'],
        $config['utilisateur'],
        $config['mdp']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function closeDatabaseConnection($pdo) {
    $pdo = null;
}

function initDatabase($pdo) {
    $sql = "-- Table des utilisateurs
        CREATE TABLE IF NOT EXISTS Utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(191) NOT NULL UNIQUE,
            mot_de_passe VARCHAR(255) NOT NULL,
            roles ENUM('inscrit', 'admin') NOT NULL DEFAULT 'inscrit'
        );

        -- Table des grilles
        CREATE TABLE IF NOT EXISTS Grilles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            createur_id INT NOT NULL,
            nom VARCHAR(255) NOT NULL,
            lignes INT NOT NULL,
            colonnes INT NOT NULL,
            definitions TEXT NOT NULL,
            solution TEXT NOT NULL,
            niveau_difficulte ENUM('debutant', 'intermediaire', 'expert') NOT NULL,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (createur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE
        );

        -- Table des sauvegardes
        CREATE TABLE IF NOT EXISTS Sauvegardes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            utilisateur_id INT NOT NULL,
            grille_id INT NOT NULL,
            etat_sauvegarde TEXT NOT NULL,
            date_sauvegarde TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (utilisateur_id) REFERENCES Utilisateurs(id) ON DELETE CASCADE,
            FOREIGN KEY (grille_id) REFERENCES Grilles(id) ON DELETE CASCADE
        );
    ";

    // Exécution du script SQL
    $pdo->exec($sql);

}



?>