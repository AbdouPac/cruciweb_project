<?php
session_start();
require_once '../model/databaseConfig.php';
require_once '../model/utilisateurModel.php';
require_once '../model/grillesModel.php';
require_once '../model/sauvegardeModel.php';

if (!isInscrit()) {
    echo json_encode(array('response' => 'error', 'message' => "Vous n'avez pas les permissions nécessaires"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = array();
        
    // Validation des champs
    if (empty($_POST['createur_id'])) {
        $errors[] = " Le createur est obligatoire";
    }
    if (empty($_POST['nom'])) {
        $errors[] = " Le nom est obligatoire";
    }
    if (empty($_POST['lignes'])) {
        $errors[] = " Le nombre de lignes est obligatoire";
    }
    if (empty($_POST['colonnes'])) {
        $errors[] = " Le nombre de colonnes est obligatoire";
    }
    if (empty($_POST['definitions'])) {
        $errors[] = " Les définitions sont obligatoires";
    }
    if (empty($_POST['solution'])) {
        $errors[] = " La solution est obligatoire";
    }
    if (empty($_POST['niveau_difficulte'])) {
        $errors[] = " Le niveau de difficulté est obligatoire";
    }
    // Si erreurs, renvoyer la réponse
    if (!empty($errors)) {
        echo json_encode(array('response' => 'error', 'message' => $errors));
        exit;
    }

    $createur_id = $_POST['createur_id'];
    $nom = $_POST['nom'];
    $lignes = $_POST['lignes'];
    $colonnes = $_POST['colonnes'];
    $definitions = $_POST['definitions'];
    $solution = $_POST['solution'];
    $niveau_difficulte = $_POST['niveau_difficulte'];

    try {
        $pdo = connectToDatabaseFromControleur();
        createGrid($pdo, $createur_id, $nom, $lignes, $colonnes, $definitions, $solution, $niveau_difficulte);
        echo json_encode(array('response' => 'success', 'message' => "Grille créée avec succès"));
        closeDatabaseConnection($pdo);
    } catch(Exception $e) {
        echo json_encode(array('response' => 'error', 'message' => $e->getMessage()));
        exit;
    }
} else {
	$arrResult = array ('response'=>'error', 'message' => "SERVER REQUEST_METHOD doit être POST");
	echo json_encode($arrResult);
}
?>