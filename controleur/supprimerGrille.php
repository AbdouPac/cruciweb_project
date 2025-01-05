<?php
session_start();
require_once '../model/databaseConfig.php';
require_once '../model/utilisateurModel.php';
require_once '../model/grillesModel.php';
require_once '../model/sauvegardeModel.php';

if (!isAdmin()) {
    echo json_encode(array('response' => 'error', 'message' => "Vous n'avez pas les permissions nécessaires"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = array();
        
    // Validation des champs
    if (empty($_POST['grille_id'])) {
        $errors[] = "L'id de la grille est obligatoire";
    }
    // Si erreurs, renvoyer la réponse
    if (!empty($errors)) {
        echo json_encode(array('response' => 'error', 'message' => $errors));
        exit;
    }

    $grille_id = $_POST['grille_id'];

    try {
        $pdo = connectToDatabaseFromControleur();
        deleteGrid($pdo, $grille_id);
        deleteSavesByGridId($pdo, $grille_id);
        echo json_encode(array('response' => 'success', 'message' => "Grille supprimée avec succès"));
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