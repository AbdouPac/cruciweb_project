<?php
session_start();
require_once '../controleur/fonctions.php';

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

        <script type="text/javascript">
            let pErreur;
            let pSucces;

            <?php
            if (isInscrit()) {
            ?>
            function supprimerSauvegarde(idGrille, idUtilisateur) {
                pErreur.classList.add('d-none');
                pSucces.classList.add('d-none');
                const formDonnees = new FormData();
                formDonnees.append('grille_id', idGrille);
                formDonnees.append('utilisateur_id', idUtilisateur);
                formDonnees.append('action', 'DELETE');

                fetch('../controleur/sauvegardes.php', {
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
