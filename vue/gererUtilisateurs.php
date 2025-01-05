<?php
session_start();

    require_once '../model/databaseConfig.php';
    require_once '../model/utilisateurModel.php';
    require_once '../model/grillesModel.php';
    require_once '../model/sauvegardeModel.php';

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
    <?php include './includes/logo.php'; ?>

    <main>
        <h1 class="text-centre">Gérer les utilisateurs</h1>
        
        <!-- Fenêtre modale -->
        <div id="modalForm" class="modal">
            <div class="modal-content">
                <span class="close" id="btnCloseModal">&times;</span>
                <h2>Ajouter un utilisateur</h2>
                <form action="../controleur/inscription.php" method="post" id="formInscription" onsubmit="submitForm()" class="form-normal">
                    <label for="email">Email :</label>
                    <input type="email" name="email" id="email" placeholder="Email" required>
                    <label for="password">Mot de passe :</label>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <label for="repassword">Confirmez le mot de passe :</label>
                    <input type="password" id="repassword" placeholder="Password" required>
                    <p id="pErreur" class="d-none erreur"></p>
                    <p id="pSucces" class="d-none success"></p>
                    <button type="submit">Inscription</button>
                </form>
            </div>
        </div>

        <br><br><hr><br><br>

        <h2 class="text-centre">Liste des utilisateurs</h2>
        <!-- Bouton pour ouvrir le modal -->
         <div class="btnModel">
            <button id="btnOpenModal">Créer utilisateur</button>
         </div>

<?php
        try {
            if (!empty($utilisateurs)) {
                echo '<table class="table-users">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>ID</th>'; // Ajout de l'en-tête pour l'ID
                echo '<th>Email</th>';
                echo '<th>Action</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($utilisateurs as $utilisateur) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($utilisateur['id']) . '</td>'; // Affichage de l'ID
                    echo '<td><a href="mailto:' . htmlspecialchars($utilisateur['email']) . '">' . htmlspecialchars($utilisateur['email']) . '</a></td>';
                    echo '<td><p class="bouton-rouge" onclick="supprimerUtilisateur(\'' . $utilisateur['email'] . '\')">Supprimer</p></td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p class="info">Aucun utilisateur disponible pour le moment.</p>';
            }
        } catch (Exception $e) {
            echo '<p class="erreur">Une erreur est survenue lors de la récupération des utilisateurs.</p>';
        }
?>

    </main>

    <?php include './includes/confirmationDeleteUser.php'; ?>

<script>
     const modal = document.getElementById("modalForm");

    if (typeof modal === "undefined") {
    const btnOpenModal = document.getElementById("btnOpenModal");
    const btnCloseModal = document.getElementById("btnCloseModal");
    const formInscription = document.getElementById("formInscription");
    const pSucces = document.getElementById("pSucces");
    const pErreur = document.getElementById("pErreur");
}
    // Gestion de l'affichage du modal
    btnOpenModal.addEventListener("click", () => {
        modal.style.display = "flex";
    });

    btnCloseModal.addEventListener("click", () => {
        // Ferme la modale et recharge la page après 2 secondes
             setTimeout(() => {
                modal.style.display = "none";
                location.reload();
            }, 1000);
    });

    // Fermer le modal en cliquant en dehors
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
             // Ferme la modale et recharge la page après 2 secondes
             setTimeout(() => {
                modal.style.display = "none";
                location.reload();
            }, 500);
            
        }
    });

    formInscription.addEventListener("submit", async (event) => {
    event.preventDefault(); 

    const password = document.getElementById("password").value;
    const repassword = document.getElementById("repassword").value;
    const modalErreur = document.getElementById("modalErreur");

    // Validation des mots de passe
    if (password !== repassword) {
        modalErreur.textContent = "Les mots de passe ne correspondent pas.";
        modalErreur.classList.remove("d-none"); // Affiche le message d'erreur
        return; 
    } else {
        modalErreur.classList.add("d-none"); // Cache l'erreur si corrigée
    }

    // Création de l'objet FormData à envoyer
    const formData = new FormData(formInscription);

    try {
        const response = await fetch(formInscription.action, {
            method: "POST",
            body: formData,
        });
        const result = await response.json();

        if (result.success) {
            pSucces.textContent = result.message;
            pSucces.classList.remove("d-none");

            modal.style.display = "none";

           
        } 
    } catch (error) {
        modalErreur.textContent = "Une erreur est survenue. Veuillez réessayer.";
        modalErreur.classList.remove("d-none");
    }
});
</script>


</body>
</html>

<?php
closeDatabaseConnection($pdo);
?>