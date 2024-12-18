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
            <p class="info"><b>Info: </b> Il n'y a pas de confirmation de suppression donc il faut faire attention avant de supprimer un utilisateur.</p>
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

        <script type="text/javascript">
            let pErreur;
            let pSucces;

            function supprimerUtilisateur(emailUtilisateur) {
                pErreur.classList.add('d-none');
                pSucces.classList.add('d-none');
                const formDonnees = new FormData();
                formDonnees.append('email', emailUtilisateur);
                formDonnees.append('action', 'DELETE');

                fetch('../controleur/gererUtilisateurs.php', {
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
                        // Trouver et supprimer dans l'affichage le li contenant l'utilisateur supprimé
                        const liens = document.querySelectorAll('.grilles-list li a');
                        liens.forEach(lien => {
                            if (lien.href.includes(emailUtilisateur)) {
                                lien.parentElement.remove();
                            }
                        });
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                });
            }

            function submitForm() {
                pErreur.classList.add('d-none');
                pSucces.classList.add('d-none');
                event.preventDefault();

                if (document.getElementById("password").value !== document.getElementById("repassword").value) {
                    pErreur.innerText = "Les mots de passe ne correspondent pas";
                    pErreur.classList.remove('d-none');
                    return;
                }
                
                const formData = new FormData(document.getElementById("formInscription"));
                formData.append('action', 'POST');
                
                fetch('../controleur/gererUtilisateurs.php', {
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
                        pSucces.innerText = data.message;
                        pSucces.classList.remove('d-none');
                        // Ajouter le nouvel utilisateur à la liste
                        const ul = document.querySelector('.grilles-list');
                        const li = document.createElement('li');
                        const a = document.createElement('a');
                        a.href = 'mailto:' + formData.get('email');
                        a.textContent = formData.get('email');
                        li.appendChild(a);
                        const p = document.createElement('p');
                        p.className = 'bouton-rouge';
                        p.onclick = () => supprimerUtilisateur(formData.get('email'));
                        p.textContent = 'Supprimer';
                        li.appendChild(p);
                        ul.appendChild(li);
                        document.getElementById("formInscription").reset();
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                });
            }

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
