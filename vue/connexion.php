<?php
    session_start();
    require_once '../model/databaseConfig.php';
    require_once '../model/utilisateurModel.php';
    require_once '../model/grillesModel.php';
    require_once '../model/sauvegardeModel.php';
    
    if (isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
         <?php include './includes/nav3.php'; ?>
         <?php include './includes/logo.php'; ?>

         <main class="connexion">

            <h1 class="text-centre">Connexion</h1>
            <p class="d-none erreur" id="erreur">Erreur lors de la connexion</p>

            <form action="../controleur/connexion.php" method="post" class="form-normal" id="formConnexion" onsubmit="submitForm()">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" placeholder="Email" required>
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="submit">Connexion</button>
                <p>Pas encore inscrit ? <a href="inscription.php">Inscrivez-vous</a></p>
            </form>
         </main>
     
        
        <script type="text/javascript">
            function submitForm() {
                const pErreur = document.getElementById("erreur");
                pErreur.classList.add('d-none');
                event.preventDefault();
                
                const formData = new FormData(document.getElementById("formConnexion"));
                
                fetch('../controleur/connexion.php', {
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
                        document.getElementById("formConnexion").reset();
                        window.location.href = 'index.php';
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                });
            }
        </script>
    </body>
</html>
