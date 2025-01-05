<!-- HTML : Ajouter une boîte de dialogue de confirmation -->
<div id="confirmationDialog" class="dialog-overlay d-none">
    <div class="dialog-box">
        <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
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
    let utilisateurEmailToDelete;

    function supprimerUtilisateur(emailUtilisateur) {
        // Afficher le dialogue personnalisé
        utilisateurEmailToDelete = emailUtilisateur;
        dialog.classList.remove('d-none');
    }

    function executeSuppression() {
        pErreur.classList.add('d-none');
        pSucces.classList.add('d-none');
        const formDonnees = new FormData();
        formDonnees.append('email', utilisateurEmailToDelete);
        formDonnees.append('action', 'DELETE');

        fetch('../controleur/gererUtilisateurs.php', {
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

                setTimeout(() => {
                    location.reload();
                }, 500);
                
                // Trouver et supprimer dans l'affichage le li contenant l'utilisateur supprimé
                const liens = document.querySelectorAll('.grilles-list li a');
                liens.forEach(lien => {
                    if (lien.href.includes(utilisateurEmailToDelete)) {
                        lien.parentElement.remove();
                    }
                });
            }
        })
        .catch((error) => {
            console.error('Erreur:', error);
        })
        .finally(() => {
            dialog.classList.add('d-none'); // Fermer la boîte de dialogue
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
