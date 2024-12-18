<?php
session_start();
require_once '../controleur/fonctions.php';
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

        <main>
            <h1>Bienvenue sur le site des mots croisés</h1>
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
                        echo ' - créée le ' . date('d/m/Y', strtotime($grille['date_creation']));
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
            } catch(Exception $e) {
                echo '<p class="erreur">Une erreur est survenue lors de la récupération des grilles.</p>';
            }
            ?>
        </main>

        <script type="text/javascript">
            let pErreur;
            let pSucces;

            <?php
            if (isAdmin()) {
            ?>
            function supprimerGrille(idGrille) {
                pErreur.classList.add('d-none');
                pSucces.classList.add('d-none');
                const formDonnees = new FormData();
                formDonnees.append('grille_id', idGrille);

                fetch('../controleur/supprimerGrille.php', {
                    method: 'POST',
                    body: formDonnees
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Succès:', data);
                    if (data.response == "error") {
                        pErreur.innerText = data.message;
                        pErreur.classList.remove('d-none');
                    } else if (data.response == "success") {
                        pSucces.innerText = data.message;
                        pSucces.classList.remove('d-none');
                        // Trouver et supprimer dans l'affichage le li contenant la grille supprimée
                        const liens = document.querySelectorAll('.grilles-list li a');
                        liens.forEach(lien => {
                            if (lien.href.includes('id=' + idGrille)) {
                                lien.parentElement.remove();
                            }
                        });
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                });
            }
            <?php
            }
            ?>

            document.addEventListener('DOMContentLoaded', function() {
                pErreur = document.getElementById('pErreur');
                pSucces = document.getElementById('pSucces');
            });
        </script>
    </body>
</html>

<?php
closeDatabaseConnection($pdo);
?>
