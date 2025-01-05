<?php
session_start();

    require_once '../model/databaseConfig.php';
    require_once '../model/utilisateurModel.php';
    require_once '../model/grillesModel.php';
    require_once '../model/sauvegardeModel.php';

$pdo = connectToDatabase();
?>
<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accueil</title>
       		
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <?php include './includes/nav.php'; ?>

        <?php  
        
            if(!isAdmin() && !isInscrit()){
                echo '<form action="../index.php" method="post" class="backward">';
                echo '<button type="submit">Retour en arrière</button>';
                echo '</form>';
            }
        ?>
        

		<?php include './includes/logo.php'; ?>

        
        <main>
            <h1>Bienvenue sur cruciWeb</h1>
            <p id="pErreur" class="d-none erreur"></p>
            <p id="pSucces" class="d-none success"></p>
            <?php
            try {
                $grilles = getGrids($pdo);

                if (!empty($grilles)) {
                    echo '<ul class="grilles-list">';
                    foreach ($grilles as $grille) {
                        echo '<li>';
                        echo '<a href="grille.php?id=' . $grille['id'] . '">';
                        echo htmlspecialchars($grille['nom']);
                        echo ' - créée le ' . date('d/m/Y à H:i', strtotime($grille['date_creation']));
                        
                        echo '</a>';
                        if (isAdmin()) {
                            echo '<p class="bouton-rouge" onclick="supprimerGrille(' . $grille['id'] . ')">Supprimer</p>';
                        }
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="info">Aucune grille disponible pour le moment.</p>';
                }
            } catch (Exception $e) {

                // Charger la configuration depuis le fichier JSON
                $configFile = '../controleur/databaseConfig.json';
                if (file_exists($configFile)) {
                    $config = json_decode(file_get_contents($configFile), true);
                    $serveur = $config['serveur'];
                }
                echo 
                    '<p 
                        class="erreur">Une erreur est survenue lors de la récupération des grilles.
                    </p>';

                echo '<p class="erreur">Veillez d\'abord vous rendre sur la page 
                            <span class="error-conf">
                              <a href="configuration.php">
                            ' . htmlspecialchars($serveur) . '/vue/configuration.php
            </a>
                            </span> 
                        pour effectuer la configuration de la base de donnée
                    </p>';
            }
            ?>
        </main>


        <div id="customDialog" class="confirmation-dialog" style="display:none;"> 
            <h2>Êtes-vous sûr de vouloir supprimer cette grille ?</h2> 
            <button id="confirmButton" class="confirm-button">Confirmer</button> 
            <button id="cancelButton" class="cancel-button">Annuler</button> 
        </div> 
        
        <div id="dialogOverlay" class="dialog-overlay" style="display:none;"></div>

      
        <?php include './includes/confirmationDeleteGrille.php'; ?>


    </body>
</html>

<?php
closeDatabaseConnection($pdo);
?>
