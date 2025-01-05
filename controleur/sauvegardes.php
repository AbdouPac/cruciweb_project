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

    if (empty($_POST['action'])) {
        $errors[] = " L'action est obligatoire";
    }

    if ($_POST['action'] == 'UPDATE') {
        if (empty($_POST['utilisateur_id'])) {
            $errors[] = " L'utilisateur est obligatoire";
        }
        if (empty($_POST['grille_id'])) {
            $errors[] = " La grille est obligatoire";
        }
        if (empty($_POST['etat_sauvegarde'])) {
            $errors[] = " L'état de la sauvegarde est obligatoire";
        }
        // Si erreurs, renvoyer la réponse
        if (!empty($errors)) {
            echo json_encode(array('response' => 'error', 'message' => $errors ));
            exit;
        }
        try {
            $pdo = connectToDatabaseFromControleur();
            if (!getSaveByUserIdAndGridId($pdo, $_POST['utilisateur_id'], $_POST['grille_id'])) {
                createSave($pdo, $_POST['utilisateur_id'], $_POST['grille_id'], $_POST['etat_sauvegarde']);
            } else {
                updateSaveByUserIdAndGridId($pdo, $_POST['utilisateur_id'], $_POST['grille_id'], $_POST['etat_sauvegarde']);
            }
            echo json_encode(array('response' => 'success', 'message' => "Sauvegarde mise à jour avec succès"));
            closeDatabaseConnection($pdo);
        } catch(Exception $e) {
            echo json_encode(array('response' => 'error', 'message' => $e->getMessage()));
            exit;
        }
    } else if ($_POST['action'] == 'DELETE') {
        if (empty($_POST['utilisateur_id'])) {
            $errors[] = " L'utilisateur est obligatoire";
        }
        if (empty($_POST['grille_id'])) {
            $errors[] = " La grille est obligatoire";
        }
        // Si erreurs, renvoyer la réponse
        if (!empty($errors)) {
            echo json_encode(array('response' => 'error', 'message' => $errors));
            exit;
        }
        try {
            $pdo = connectToDatabaseFromControleur();
            deleteSavesByUserIdAndGridId($pdo, $_POST['utilisateur_id'], $_POST['grille_id']);
            echo json_encode(array('response' => 'success', 'message' => "Sauvegarde supprimée avec succès"));
            closeDatabaseConnection($pdo);
        } catch(Exception $e) {
            echo json_encode(array('response' => 'error', 'message' => $e->getMessage()));
            exit;
        }
    }
} else {
	$arrResult = array ('response'=>'error', 'message' => "SERVER REQUEST_METHOD doit être POST");
	echo json_encode($arrResult);
}
?>