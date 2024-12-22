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
    // try {
    //     echo json_encode(array('response' => 'success', 'message' => "Base de données et tables créées avec succès."));
    //     exit;
    // } catch (PDOException $e) {
    //     echo json_encode(array('response' => 'error', 'message' => $e->getMessage()));
    //     exit;
    // }
}

// Fonctions pour la table Utilisateurs
function createUser($pdo, $email, $mot_de_passe, $roles) {
    $sql = "INSERT INTO Utilisateurs (email, mot_de_passe, roles) VALUES (:email, :mot_de_passe, :roles)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email, ':mot_de_passe' => password_hash($mot_de_passe, PASSWORD_DEFAULT), ':roles' => $roles]);
}

function getUserByEmail($pdo, $email) {
    $sql = "SELECT * FROM Utilisateurs WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserIdByEmail($pdo, $email) {
    $sql = "SELECT id FROM Utilisateurs WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUsers($pdo) {
    $sql = "SELECT * FROM Utilisateurs";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUsersByRole($pdo, $role) {
    $sql = "SELECT * FROM Utilisateurs WHERE roles = :role";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':role' => $role]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function loginUser($pdo, $email, $mot_de_passe) {
    $user = getUserByEmail($pdo, $email);
    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['roles'];
        return true;
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function logoutUser() {
    session_destroy();
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isAdminExist($pdo) {
    $sql = "SELECT * FROM Utilisateurs WHERE roles = 'admin'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

function isInscrit() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'inscrit';
}

function deleteUser($pdo, $id) {
    $sql = "DELETE FROM Utilisateurs WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

function deleteUserByEmail($pdo, $email) {
    $sql = "DELETE FROM Utilisateurs WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
}

// Fonctions pour la table Grilles
function createGrid($pdo, $createur_id, $nom, $lignes, $colonnes, $definitions, $solution, $niveau_difficulte) {
    $sql = "INSERT INTO Grilles (createur_id, nom, lignes, colonnes, definitions, solution, niveau_difficulte) VALUES (:createur_id, :nom, :lignes, :colonnes, :definitions, :solution, :niveau_difficulte)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':createur_id' => $createur_id, ':nom' => $nom, ':lignes' => $lignes, ':colonnes' => $colonnes, ':definitions' => $definitions, ':solution' => $solution, ':niveau_difficulte' => $niveau_difficulte]);
}

function getGridById($pdo, $id) {
    $sql = "SELECT * FROM Grilles WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getGrids($pdo) {
    $sql = "SELECT * FROM Grilles";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function verifierGrille($pdo, $grille_id, $etat_sauvegarde) {
    $sql = "SELECT * FROM Grilles WHERE id = :grille_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':grille_id' => $grille_id]);
    $grille = $stmt->fetch(PDO::FETCH_ASSOC);
    $solution = json_decode($grille['solution'], true);
    $resultat = "";
    for ($i = 0; $i < strlen($grille['solution']); $i++) {
        if ($grille['solution'][$i] == $etat_sauvegarde[$i]) {
            $resultat[$i] = $grille['solution'][$i];
        } else {
            $resultat[$i] = '_';
        }
    }
    return $resultat;
}

function deleteGrid($pdo, $id) {
    $sql = "DELETE FROM Grilles WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}


// Fonctions pour la table Sauvegardes
function createSave($pdo, $utilisateur_id, $grille_id, $etat_sauvegarde) {
    $sql = "INSERT INTO Sauvegardes (utilisateur_id, grille_id, etat_sauvegarde) VALUES (:utilisateur_id, :grille_id, :etat_sauvegarde)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':utilisateur_id' => $utilisateur_id, ':grille_id' => $grille_id, ':etat_sauvegarde' => $etat_sauvegarde]);
}

function getSavesByUserId($pdo, $utilisateur_id) {
    $sql = "SELECT * FROM Sauvegardes WHERE utilisateur_id = :utilisateur_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSavedGridsByUserId($pdo, $utilisateur_id) {
    // Récupérer d'abord toutes les sauvegardes de l'utilisateur
    $sql = "SELECT grille_id FROM Sauvegardes WHERE utilisateur_id = :utilisateur_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
    $sauvegardes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si aucune sauvegarde, retourner un tableau vide
    if (empty($sauvegardes)) {
        return array();
    }

    // Extraire les IDs des grilles des sauvegardes
    $grille_ids = array_column($sauvegardes, 'grille_id');

    // Construire la requête pour récupérer les grilles correspondantes
    $placeholders = str_repeat('?,', count($grille_ids) - 1) . '?';
    $sql = "SELECT * FROM Grilles WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($grille_ids);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getSaveByUserIdAndGridId($pdo, $utilisateur_id, $grille_id) {
    $sql = "SELECT * FROM Sauvegardes WHERE utilisateur_id = :utilisateur_id AND grille_id = :grille_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':utilisateur_id' => $utilisateur_id, ':grille_id' => $grille_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getSaveById($pdo, $id) {
    $sql = "SELECT * FROM Sauvegardes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateSave($pdo, $id, $etat_sauvegarde) {
    $sql = "UPDATE Sauvegardes SET etat_sauvegarde = :etat_sauvegarde WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':etat_sauvegarde' => $etat_sauvegarde, ':id' => $id]);
}

function updateSaveByUserIdAndGridId($pdo, $utilisateur_id, $grille_id, $etat_sauvegarde) {
    $sql = "UPDATE Sauvegardes SET etat_sauvegarde = :etat_sauvegarde WHERE utilisateur_id = :utilisateur_id AND grille_id = :grille_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':etat_sauvegarde' => $etat_sauvegarde, ':utilisateur_id' => $utilisateur_id, ':grille_id' => $grille_id]);
}

function deleteSave($pdo, $id) {
    $sql = "DELETE FROM Sauvegardes WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
}

function deleteSavesByGridId($pdo, $grille_id) {
    $sql = "DELETE FROM Sauvegardes WHERE grille_id = :grille_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':grille_id' => $grille_id]);
}

function deleteSavesByUserIdAndGridId($pdo, $utilisateur_id, $grille_id) {
    $sql = "DELETE FROM Sauvegardes WHERE utilisateur_id = :utilisateur_id AND grille_id = :grille_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':utilisateur_id' => $utilisateur_id, ':grille_id' => $grille_id]);
}

function deleteSavesByUserId($pdo, $utilisateur_id) {
    $sql = "DELETE FROM Sauvegardes WHERE utilisateur_id = :utilisateur_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':utilisateur_id' => $utilisateur_id]);
}

