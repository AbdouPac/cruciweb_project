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
    $email = "";
    $password = "";
        
    
    if (isset($_POST['action']) && $_POST['action'] === 'POST') {
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
                echo json_encode(array('response' => 'error', 'message' => "Cette email est déjà utilisé"));
                exit;
            }
            createUser($pdo, $email, $password, "inscrit");
            echo json_encode(array('response' => 'success', 'message' => "Utilisateur ".$email." créé avec succès"));
            closeDatabaseConnection($pdo);
        } catch(Exception $e) {
            echo json_encode(array('response' => 'error', 'message' => $e->getMessage()));
            exit;
        }
    } else if (isset($_POST['action']) && $_POST['action'] === 'DELETE') {
        // Validation des champs
        if (empty($_POST['email'])) {
            $errors[] = " Le mail est obligatoire";
        }
        // Si erreurs, renvoyer la réponse
        if (!empty($errors)) {
            echo json_encode(array('response' => 'error', 'message' => $errors));
            exit;
        }
    
        $email = $_POST['email'];
    
        try {
            $pdo = connectToDatabaseFromControleur();
            if (getUserByEmail($pdo, $email)) {
                deleteUserByEmail($pdo, $email);
                $user_id = getUserIdByEmail($pdo, $email);
                deleteSavesByUserId($pdo, $user_id);
                echo json_encode(array('response' => 'success', 'message' => "Utilisateur ".$email." supprimé avec succès"));
            }
            closeDatabaseConnection($pdo);
        } catch(Exception $e) {
            echo json_encode(array('response' => 'error', 'message' => $e->getMessage()));
            exit;
        }
    } else {
        echo json_encode(array('response' => 'error', 'message' => 'Le paramètre action doit être POST ou DELETE'));
    }
} else {
	$arrResult = array ('response'=>'error', 'message' => "SERVER REQUEST_METHOD doit être POST");
	echo json_encode($arrResult);
}
?>