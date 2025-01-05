<?php
    session_start();
    require_once '../model/databaseConfig.php';
    require_once '../model/utilisateurModel.php';
    require_once '../model/grillesModel.php';
    require_once '../model/sauvegardeModel.php';
    ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Configuration</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <?php include './includes/nav2.php' ?>

        <h2 class="text-centre">Configurations</h2>
        <p class="d-none erreur" id="erreur">Erreur lors de la configuration de la base de données</p>
        <p class="d-none success" id="success">Configuration enregistrée avec succès</p>
        <form action="../controleur/configuration.php" method="POST" class="form-normal" id="formConfig" onsubmit="submitForm()">
            <label for="serveur">Adresse du serveur :</label>
            <input type="text" id="serveur" name="serveur" required>
            
            <label for="bdd">Nom de la base de données :</label>
            <input type="text" id="bdd" name="bdd" required>
            
            <label for="utilisateur">Nom d'utilisateur :</label>
            <input type="text" id="utilisateur" name="utilisateur" required>

            <label for="mdp">Mot de passe :</label>
            <input type="password" id="mdp" name="mdp">
            
            <button type="submit">Valider</button>
        </form>

        <script type="text/javascript">
            function submitForm() {
                const pErreur = document.getElementById("erreur");
                const pSuccess = document.getElementById("success");
                pErreur.classList.add('d-none');
                pSuccess.classList.add('d-none');
                event.preventDefault();
                
                const formData = new FormData(document.getElementById("formConfig"));
                
                fetch('../controleur/configuration.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Succès:', data);
                    if (data.response == "error") {
                        pErreur.innerText = data.message;
                        pErreur.classList.remove('d-none');
                    } else if (data.response == "success") {
                        pSuccess.innerText = data.message;
                        pSuccess.classList.remove('d-none');
                        document.getElementById("formConfig").reset();
                    } else {
                        pErreur.innerText = "Une erreur inconnue est survenue";
                        pErreur.classList.remove('d-none');
                        console.error('Erreur:', data);
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                });
            }
        </script>
    </body>
</html>