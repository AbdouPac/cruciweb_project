<?php
session_start();
require_once '../model/databaseConfig.php';
require_once '../model/utilisateurModel.php';
require_once '../model/grillesModel.php';
require_once '../model/sauvegardeModel.php';

if (isLoggedIn()) {
    echo json_encode(array('response' => 'error', 'message' => "Vous êtes déjà connecté"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = array();
        
    // Validation des champs
    if (empty($_POST['email'])) {
        $errors[] = " Le mail est obligatoire";
    }
    if (empty($_POST['password'])) {
        $errors[] = " Le mot de passe est obligatoire";
    }
    // Si erreurs, renvoyer la réponse
    if (!empty($errors)) {
        echo json_encode(array('response' => 'error', 'message' => $errors));
        exit;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $pdo = connectToDatabaseFromControleur();
        if (getUserByEmail($pdo, $email)) {
            echo json_encode(array('response' => 'error', 'message' => "Un compte utilisateur existe déjà avec cette adresse mail"));
            exit;
        }
        createUser($pdo, $email, $password, "inscrit");
        if (loginUser($pdo, $email, $password)) {
            echo json_encode(array('response' => 'success', 'message' => "Utilisateur ".$email." créé avec succès"));
        } else {
            echo json_encode(array('response' => 'error', 'message' => "Email ou mot de passe incorrect"));
        }
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
