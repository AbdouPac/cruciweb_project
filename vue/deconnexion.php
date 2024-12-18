<?php
session_start();
require_once '../controleur/fonctions.php';
logoutUser();
header('Location: index.php');
?>