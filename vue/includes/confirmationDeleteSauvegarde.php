<!-- HTML : Boîte de dialogue de confirmation -->
<div id="confirmationDialog" class="dialog-overlay d-none">
    <div class="dialog-box">
        <p>Êtes-vous sûr de vouloir supprimer cette sauvegarde ?</p>
        <div class="dialog-buttons">
            <button id="btnConfirmer" class="btn btn-danger">Confirmer</button>
            <button id="btnAnnuler" class="btn btn-secondary">Annuler</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    let pErreur;
    let pSucces;
    let dialog;
    let btnConfirmer;
    let btnAnnuler;
    let grilleIdToDelete;
    let utilisateurIdToDelete;

    function supprimerSauvegarde(idGrille, idUtilisateur) {
        // Afficher le dialogue personnalisé pour confirmation
        grilleIdToDelete = idGrille;
        utilisateurIdToDelete = idUtilisateur;
        dialog.classList.remove('d-none');
    }

    function executeSuppression() {
        pErreur.classList.add('d-none');
        pSucces.classList.add('d-none');

        const formDonnees = new FormData();
        formDonnees.append('grille_id', grilleIdToDelete);
        formDonnees.append('utilisateur_id', utilisateurIdToDelete);
        formDonnees.append('action', 'DELETE');

        fetch('../controleur/sauvegardes.php', {
            method: 'POST',
            body: formDonnees
        })
        .then(response => response.json())
        .then(data => {
            console.log('Succès:', data);
            if (data.response === "error") {
                pErreur.innerText = data.message;
                pErreur.classList.remove('d-none');
            } else if (data.response === "success") {
                pSucces.innerText = data.message;
                pSucces.classList.remove('d-none');
                // Trouver et supprimer dans l'affichage le li contenant la sauvegarde supprimée
                const liens = document.querySelectorAll('.grilles-list li a');
                liens.forEach(lien => {
                    if (lien.href.includes('id=' + grilleIdToDelete)) {
                        lien.parentElement.remove();
                    }
                });
                // Attendre 2 secondes avant de rafraîchir la page
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        })
        .catch((error) => {
            console.error('Erreur:', error);
        })
        .finally(() => {
            dialog.classList.add('d-none'); // Fermer la boîte de dialogue
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        pErreur = document.getElementById('pErreur');
        pSucces = document.getElementById('pSucces');
        dialog = document.getElementById('confirmationDialog');
        btnConfirmer = document.getElementById('btnConfirmer');
        btnAnnuler = document.getElementById('btnAnnuler');

        // Gestion des boutons de la boîte de dialogue
        btnConfirmer.addEventListener('click', executeSuppression);
        btnAnnuler.addEventListener('click', () => {
            dialog.classList.add('d-none'); // Masquer la boîte de dialogue
        });
    });
</script>
