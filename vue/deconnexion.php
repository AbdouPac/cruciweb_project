<?php
session_start();

    require_once '../model/databaseConfig.php';
    require_once '../model/utilisateurModel.php';
    require_once '../model/grillesModel.php';
    require_once '../model/sauvegardeModel.php';

logoutUser();
header('Location: index.php');
?>