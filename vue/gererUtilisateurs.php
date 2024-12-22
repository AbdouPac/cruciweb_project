<?php
session_start();
require_once '../controleur/fonctions.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$pdo = connectToDatabase();
$utilisateurs = getUsersByRole($pdo, 'inscrit');
?>
<!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gérer les utilisateurs</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <?php include './includes/nav.php'; ?>

        <main>
            <h1 class="text-centre">Gérer les utilisateurs</h1>
            <p id="pErreur" class="d-none erreur"></p>
            <p id="pSucces" class="d-none success"></p>

            <h2 class="text-centre">Ajouter un utilisateur</h2>
            <form action="../controleur/inscription.php" method="post" id="formInscription" onsubmit="submitForm()" class="form-normal">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" placeholder="Email" required>
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <label for="repassword">Confirmez le mot de passe :</label>
                <input type="password" id="repassword" placeholder="Password" required>
                <button type="submit">Inscription</button>
            </form>

            <br><br><hr><br><br>

            <h2 class="text-centre">Liste des utilisateurs</h2>
            <?php
            try {
                if (!empty($utilisateurs)) {
                    echo '<ul class="grilles-list">';
                    foreach ($utilisateurs as $utilisateur) {
                        echo '<li>';
                        echo '<a href="mailto:' . htmlspecialchars($utilisateur['email']) . '">';
                        echo htmlspecialchars($utilisateur['email']);
                        echo '</a>';
                        echo '<p class="bouton-rouge" onclick="supprimerUtilisateur(\'' . $utilisateur['email'] . '\')">Supprimer</p>';
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="info">Aucun utilisateur disponible pour le moment.</p>';
                }
            } catch(Exception $e) {
                echo '<p class="erreur">Une erreur est survenue lors de la récupération des utilisateurs.</p>';
            }
            ?>
        </main>

        <?php include './includes/confirmationDeleteUser.php'; ?>
    </body>
</html>

<?php
closeDatabaseConnection($pdo);
?>
