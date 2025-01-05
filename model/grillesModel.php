<?php



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


?>