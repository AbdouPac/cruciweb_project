<?php
session_start();

    require_once '../model/databaseConfig.php';
    require_once '../model/utilisateurModel.php';
    require_once '../model/grillesModel.php';
    require_once '../model/sauvegardeModel.php';

if (!isInscrit()) {
    header('Location: index.php');
    exit;
}

$pdo = connectToDatabase();
?>
<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Grilles sauvegardées</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <?php include './includes/nav.php'; ?>

        <main>
            <h1>Grilles sauvegardées</h1>
            <p id="pErreur" class="d-none erreur"></p>
            <p id="pSucces" class="d-none success"></p>
            <?php
            try {
                $grilles = getSavedGridsByUserId($pdo, $_SESSION['user_id']);
                
                if (!empty($grilles)) {
                    echo '<ul class="grilles-list">';
                    foreach ($grilles as $grille) {
                        echo '<li>';
                        echo '<a href="grille.php?id=' . $grille['id'] . '">';
                        echo htmlspecialchars($grille['nom']);
                        echo ' - sauvegardée le ' . date('d/m/Y', strtotime($grille['date_creation']));
                        echo '</a>';
                        echo '<p class="bouton-rouge" onclick="supprimerSauvegarde(' . $grille['id'] . ', ' . $_SESSION['user_id'] . ')">Supprimer</p>';
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="info">Aucune sauvegarde disponible pour le moment.</p>';
                }
            } catch(Exception $e) {
                echo '<p class="erreur">Une erreur est survenue lors de la récupération des Sauvegardes.</p>';
            }
            ?>
        </main>

        <?php include './includes/confirmationDeleteSauvegarde.php'; ?>
    </body>
</html>

<?php
closeDatabaseConnection($pdo);
?>
