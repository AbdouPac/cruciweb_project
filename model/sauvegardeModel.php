<?php


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

?>