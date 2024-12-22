<?php

session_start();

header('Content-type: application/json');

require_once './fonctions.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$errors = array();
        
    // Validation des champs
    if (empty($_POST['serveur'])) {
        $errors[] = " Le serveur est obligatoire";
    }
    if (empty($_POST['bdd'])) {
        $errors[] = " Le nom de la base de données est obligatoire";
    }
    if (empty($_POST['utilisateur'])) {
        $errors[] = " Le nom d'utilisateur est obligatoire";
    }
    // Si erreurs, renvoyer la réponse
    if (!empty($errors)) {
        echo json_encode(array('response' => 'error', 'message' => $errors));
        exit;
    }

    $params['serveur'] = $_POST['serveur'];
    $params['bdd'] = $_POST['bdd'];
    $params['utilisateur'] = $_POST['utilisateur'];
    $params['mdp'] = $_POST['mdp'];

    try {
        $pdo = connectToDatabaseParams($params['serveur'], $params['bdd'], $params['utilisateur'], $params['mdp']);
        saveDatabaseConfig($params);
        initDatabase($pdo);
        // Création de l'utilisateur admin par défaut si il n'existe pas
        if (!isAdminExist($pdo)) {
            createUser($pdo, 'admin@admin.com', 'admin', 'admin');
        }
        closeDatabaseConnection($pdo);
        echo json_encode(array('response'=>'success', 'message' => "Configuration enregistrée avec succès : " . json_encode($params) . ", base de données initialisée et utilisateur admin créé."));
    } catch(Exception $e) {
        echo json_encode(array('response' => 'error', 'message' => $e->getMessage()));
        exit;
    }

} else {
	$arrResult = array ('response'=>'error', 'message' => "SERVER REQUEST_METHOD doit être POST");
	echo json_encode($arrResult);
}
?>
