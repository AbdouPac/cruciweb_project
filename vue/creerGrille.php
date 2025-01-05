<?php
session_start();
    require_once '../model/databaseConfig.php';
    require_once '../model/utilisateurModel.php';
    require_once '../model/grillesModel.php';
    require_once '../model/sauvegardeModel.php';

if (!isInscrit()) {
    header('Location: ../vue/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Créer une grille</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <?php include './includes/nav.php'; ?>

        <h1 class="text-centre">Créer une grille</h1>
        <p class="d-none erreur" id="erreur">Erreur lors de la création de la grille</p>
        <form action="../controleur/creerGrille.php" method="post" id="formGrille" onsubmit="submitForm()">
            <div id="partie1">
                <h3 class="text-centre">Partie 1 : Propriétés de la grille</h3>
                
                <div class="form-normal">
                    <label for="nom">Nom de la grille :</label>
                    <input type="text" name="nom" id="nom" placeholder="Nom de la grille" required>
    
                    <label for="lignes">Nombre de lignes :</label>
                    <input type="number" name="lignes" id="lignes" min="2" max="100" placeholder="Nombre de lignes" required>
                    
                    <label for="colonnes">Nombre de colonnes :</label>
                    <input type="number" name="colonnes" id="colonnes" min="2" max="100" placeholder="Nombre de colonnes" required>
                    
                    <label for="niveau_difficulte">Niveau de difficulté :</label>
                    <select name="niveau_difficulte" id="niveau_difficulte" required>
                        <option value="debutant">Débutant</option>
                        <option value="intermediaire">Intermédiaire</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>

                <p class="warning">Attention, si vous <b>modifiez le nombre de ligne ou de colonne</b>, vous <u>perdrez toutes les données saisies aux Parties 2 et 3 !</u></p>
                <div class="position-right">
                    <button type="button" onclick="afficherPartie(2)">Suivant</button>
                </div>
            </div>

            <div id="partie2" class="d-none">
                <h3 class="text-centre">Partie 2 : Remplissage de la grille</h3>
                <div id="grille"></div>

                <p class="info">Cliquez sur une case pour la remplir. <b>Vous pouvez également mettre # pour des cases noires de séparation</b>.</p>
                <div class="space-between">
                    <button type="button" onclick="afficherPartie(1)">Précedent</button>
                    <button type="button" onclick="afficherPartie(3)">Suivant</button>
                </div>
            </div>

            <div id="partie3" class="d-none">
                <h3 class="text-centre">Partie 3 : Définitions des mots</h3>
                <p class="info">Vous devez donner les définitions des mots de la grille. <b>Vous devez séparer les définitions d'une même ligne/colonne par un <u><i>#</i></u>.</b></p>

                <div id="definitions" class="form-normal"></div>

                <div class="space-between">
                    <button type="button" onclick="afficherPartie(2)">Précedent</button>
                    <button type="submit">Créer la grille</button>
                </div>
            </div>

            <input type="hidden" name="createur_id" id="createur_id" value="<?php echo $_SESSION['user_id']; ?>">

        </form>

        <form action="" method="post" id="formDonnees" class="d-none">
        </form>

        <script type="text/javascript" defer>
            let pErreur;
            let lignes;
            let colonnes;
            let clesDefinitionsLettres = [];
            let clesDefinitionsChiffres = [];
            let nbMotsLignes = [];
            let nbMotsColonnes = [];

            function validerEntreeGrille(input) {
                // Vérifier que l'entrée est une lettre unique
                if (input.value.length == 1) {
                    // Forcer en majuscule
                    input.value = input.value.toUpperCase();
                    // Ne garder que les lettres et le #
                    input.value = input.value.replace(/[^A-Z#]/g, '');
                    if (input.value == "#") {
                        input.style.backgroundColor = "black";
                        input.style.color = "gray";
                    }
                    if (input.value != "") {
                        // Mettre le focus sur l'input suivant
                        const inputs = document.querySelectorAll('.case');
                        const currentIndex = Array.from(inputs).indexOf(input);
                        if (currentIndex < inputs.length - 1) {
                            inputs[currentIndex + 1].focus();
                        }
                    }
                } else {
                    input.value = "";
                    input.style.backgroundColor = "";
                    input.style.color = "";
                }
            }

            function creerGrilleVide() {
                const grille = document.getElementById("grille");
                grille.innerHTML = "";
                lignes = document.getElementById("lignes").value;
                colonnes = document.getElementById("colonnes").value;
                clesDefinitionsLettres = [];
                clesDefinitionsChiffres = [];

                let numerotationLettres = document.createElement("div");
                numerotationLettres.classList.add("ligne");
                let caseNumerotationLettresVide = document.createElement("input");
                caseNumerotationLettresVide.classList.add("numerotation");
                caseNumerotationLettresVide.disabled = true;
                caseNumerotationLettresVide.value = " ";
                numerotationLettres.appendChild(caseNumerotationLettresVide);
                for (let j = 0; j < colonnes; j++) {
                    const lettre = document.createElement("input");
                    lettre.classList.add("numerotation");
                    lettre.disabled = true;
                    lettre.value = String.fromCharCode(65 + j);
                    numerotationLettres.appendChild(lettre);
                    clesDefinitionsLettres.push(lettre.value);
                }
                grille.appendChild(numerotationLettres);
                
                for (let i = 0; i < lignes; i++) {
                    const ligne = document.createElement("div");
                    ligne.classList.add("ligne");
                    let numerotationChiffres = document.createElement("input");
                    numerotationChiffres.classList.add("numerotation");
                    numerotationChiffres.type = "text";
                    numerotationChiffres.disabled = true;
                    numerotationChiffres.value = i + 1;
                    ligne.appendChild(numerotationChiffres);
                    clesDefinitionsChiffres.push(numerotationChiffres.value);
                    for (let j = 0; j < colonnes; j++) {
                        const input = document.createElement("input");
                        input.id = "case_" + i + "" + j;
                        input.type = "text";
                        input.maxLength = 1;
                        input.required = true;
                        input.classList.add("case");
                        input.dataset.ligne = i;
                        input.dataset.colonne = j;
                        input.addEventListener("input", () => validerEntreeGrille(input));
                        ligne.appendChild(input);
                    }
                    grille.appendChild(ligne);
                }
            }

            function calculerNbMots() {
                for (let i=0; i<colonnes; i++) {
                    let phrase = "";
                    nbMotsColonnes[i] = 0;
                    for (let j=0; j<lignes; j++) {
                        phrase += document.getElementById("case_" + j + "" + i).value;
                    }
                    for (let mot of phrase.split("#")) {
                        if (mot != "") nbMotsColonnes[i]++;
                    }
                }
                for (let i=0; i<lignes; i++) {
                    let phrase = "";
                    nbMotsLignes[i] = 0;
                    for (let j=0; j<colonnes; j++) {
                        phrase += document.getElementById("case_" + i + "" + j).value;
                    }
                    for (let mot of phrase.split("#")) {
                        if (mot != "") nbMotsLignes[i]++;
                    }
                }
                console.log("nbMotsColonnes : ", nbMotsColonnes);
                console.log("nbMotsLignes : ", nbMotsLignes);
            }

            function creerChampsDefinitions() {
                const definitions = document.getElementById("definitions");
                definitions.innerHTML = "";
                
                const titreLettre = document.createElement("h3");
                titreLettre.innerText = "Définitions des colonnes :";
                definitions.appendChild(titreLettre);
                
                for (let lettre of clesDefinitionsLettres) {
                    const champ = document.createElement("input");
                    champ.type = "textarea";
                    champ.placeholder = lettre;
                    champ.id = "definition_" + lettre;
                    const label = document.createElement("label");
                    label.innerText = "Colonne " + lettre + " :";
                    label.for = "definition_" + lettre;
                    definitions.appendChild(label);
                    definitions.appendChild(champ);
                }

                const titreLigne = document.createElement("h3");
                titreLigne.innerText = "Définitions des lignes :";
                definitions.appendChild(titreLigne);

                for (let chiffre of clesDefinitionsChiffres) {
                    const champ = document.createElement("input");
                    champ.type = "textarea";
                    champ.placeholder = chiffre;
                    champ.id = "definition_" + chiffre;
                    const label = document.createElement("label");
                    label.innerText = "Ligne " + chiffre + " :";
                    label.for = "definition_" + chiffre;
                    definitions.appendChild(label);
                    definitions.appendChild(champ);
                }
            }

            function definitionsValides() {
                const definitions = document.getElementById("definitions");
                const inputs = definitions.querySelectorAll("input");
                for (let input of inputs) {
                    if (input.value == "") return false;
                }
                for (let i=0; i<colonnes; i++) {
                    let nb = 0;
                    for (let mot of document.getElementById("definition_" + clesDefinitionsLettres[i]).value.split("#")) {
                        if (mot != "") nb++;
                    }
                    if (nb != nbMotsColonnes[i]) return false;
                }
                for (let i=0; i<lignes; i++) {
                    let nb = 0;
                    for (let mot of document.getElementById("definition_" + clesDefinitionsChiffres[i]).value.split("#")) {
                        if (mot != "") nb++;
                    }
                    if (nb != nbMotsLignes[i]) return false;
                }
                return true;
            }

            function remplirFormDonnees() {
                const formDonnees = document.getElementById("formDonnees");
                formDonnees.innerHTML = "";
                formDonnees.appendChild(document.getElementById("createur_id"));
                formDonnees.appendChild(document.getElementById("nom"));
                formDonnees.appendChild(document.getElementById("lignes"));
                formDonnees.appendChild(document.getElementById("colonnes"));

                const definitions = document.createElement("textarea");
                definitions.name = "definitions";
                definitions.value = "";
                let definitionsJson = {};
                for (let i=0; i<colonnes; i++) {
                    definitionsJson[clesDefinitionsLettres[i]] = document.getElementById("definition_" + clesDefinitionsLettres[i]).value;
                }
                for (let i=0; i<lignes; i++) {
                    definitionsJson[clesDefinitionsChiffres[i]] = document.getElementById("definition_" + clesDefinitionsChiffres[i]).value;
                }
                definitions.value = JSON.stringify(definitionsJson);
                formDonnees.appendChild(definitions);

                const solution = document.createElement("textarea");
                solution.name = "solution";
                solution.value = "";
                let solutionJson = {};
                for (let i=0; i<lignes; i++) {
                    solutionJson[clesDefinitionsChiffres[i]] = "";
                    for (let j=0; j<colonnes; j++) {
                        solutionJson[clesDefinitionsChiffres[i]] += document.getElementById("case_" + i + "" + j).value;
                    }
                }
                solution.value = JSON.stringify(solutionJson);
                formDonnees.appendChild(solution);

                formDonnees.appendChild(document.getElementById("niveau_difficulte"));
            }

            function partie1NonValide() {
                const lignes = document.getElementById("lignes").value;
                const colonnes = document.getElementById("colonnes").value;
                const nomGrille = document.getElementById("nom").value;
                if (lignes < 2 || colonnes < 2 || lignes > 100 || colonnes > 100 || nomGrille == "") {
                    return true;
                }
                return false;
            }

            function grilleNonValide() {
                const grille = document.getElementById("grille");
                const inputs = grille.querySelectorAll("input");
                for (let input of inputs) {
                    if (input.value == "") return true;
                }
                return false;
            }

            function afficherPartie(partie) {
                pErreur.classList.add('d-none');
                let partie1 = document.getElementById("partie1");
                let partie2 = document.getElementById("partie2");
                let partie3 = document.getElementById("partie3");

                if (partie == 1) {
                    partie1.classList.remove('d-none');
                    partie2.classList.add('d-none');
                    partie3.classList.add('d-none');
                } else if (partie == 2) {
                    if (partie1NonValide()) {
                        pErreur.innerText = "Veuillez remplir tous les champs avec des valeurs valides.";
                        pErreur.classList.remove('d-none');
                    } else {
                        partie1.classList.add('d-none');
                        partie2.classList.remove('d-none');
                        partie3.classList.add('d-none');
                    }
                } else if (partie == 3) {
                    if (grilleNonValide()) {
                        pErreur.innerText = "La grille n'est pas valide. Remplissez toutes les cases.";
                        pErreur.classList.remove('d-none');
                    } else {
                        calculerNbMots();
                        creerChampsDefinitions();
                        partie1.classList.add('d-none');
                        partie2.classList.add('d-none');
                        partie3.classList.remove('d-none');
                    }
                }
            }

            function submitForm() {
                pErreur.classList.add('d-none');
                event.preventDefault();

                //if (!definitionsValides()) {
                if (false) {
                    pErreur.innerHTML = "Veuillez remplir toutes les définitions. <b>Et séparer les définitions d'une même ligne/colonne par un <u><i>#</i></u>.</b>";
                    pErreur.classList.remove('d-none');
                    return;
                }
                remplirFormDonnees();
                
                const formData = new FormData(document.getElementById("formDonnees"));
                
                fetch('../controleur/creerGrille.php', {
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
                        document.getElementById("formGrille").reset();
                        document.getElementById("formDonnees").reset();
                        window.location.href = 'index.php';
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById("lignes").addEventListener("change", () => {
                    creerGrilleVide();
                });
                document.getElementById("colonnes").addEventListener("change", () => {
                    creerGrilleVide();
                });

                pErreur = document.getElementById("erreur");
            });
        </script>
    </body>
</html>