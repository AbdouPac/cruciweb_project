<?php

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

?>