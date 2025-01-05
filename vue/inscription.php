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
        <title>Inscription</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>

        <?php include './includes/nav3.php'; ?>
        <?php include './includes/logo.php'; ?>

        <main class="inscription">
            <h1 class="text-centre">Inscription</h1>
            <p class="d-none erreur" id="erreur">Erreur lors de l'inscription</p>

            <form action="../controleur/inscription.php" method="post" id="formInscription" onsubmit="submitForm()" class="form-normal">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" placeholder="Email" required>
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <label for="repassword">Confirmez le mot de passe :</label>
                <input type="password" id="repassword" placeholder="Password" required>
                <button type="submit">Inscription</button>
                <p class="text-centre">Vous avez déjà un compte ? <a href="connexion.php">Connectez-vous</a></p>
            </form>
        </main>

       
        <script type="text/javascript">
            
    function submitForm() {
        const pErreur = document.getElementById("erreur");
        pErreur.classList.add('d-none');
        event.preventDefault();

        const password = document.getElementById("password").value;
        const repassword = document.getElementById("repassword").value;

        // Vérification des mots de passe identiques
        if (password !== repassword) {
            pErreur.innerText = "Les mots de passe ne correspondent pas";
            pErreur.classList.remove('d-none');
            return;
        }

        // Validation du mot de passe
        const passwordValidationMessage = validatePassword(password);
        if (passwordValidationMessage !== true) {
            pErreur.innerText = passwordValidationMessage;
            pErreur.classList.remove('d-none');
            return;
        }

        const formData = new FormData(document.getElementById("formInscription"));

        fetch('../controleur/inscription.php', {
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
                document.getElementById("formInscription").reset();
                window.location.href = 'index.php';
            }
        })
        .catch((error) => {
            console.error('Erreur:', error);
        });
    }

    function validatePassword(password) {
    let messages = [];

    if (password.length < 8) {
        messages.push("- Le mot de passe doit contenir au moins 8 caractères.");
    }
    if (!/[A-Z]/.test(password)) {
        messages.push("- Le mot de passe doit contenir au moins une lettre majuscule.");
    }
    if (!/[a-z]/.test(password)) {
        messages.push("- Le mot de passe doit contenir au moins une lettre minuscule.");
    }
    if (!/\d/.test(password)) {
        messages.push("- Le mot de passe doit contenir au moins un chiffre.");
    }
    if (!/[\W_]/.test(password)) {
        messages.push("- Le mot de passe doit contenir au moins un caractère spécial.");
    }

    return messages.length > 0 ? messages.join("\n") : true;
}

</script>

    </body>
</html>
