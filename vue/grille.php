<?php
session_start();

    require_once '../model/databaseConfig.php';
    require_once '../model/utilisateurModel.php';
    require_once '../model/grillesModel.php';
    require_once '../model/sauvegardeModel.php';

$pdo = connectToDatabase();
$grille = getGridById($pdo, $_GET['id']);
$sauvegardeTrouvee = null;
$userId = null;
if (isInscrit()) {
    $sauvegardeTrouvee = getSaveByUserIdAndGridId($pdo, $_SESSION['user_id'], $_GET['id']);
    $userId = $_SESSION['user_id'];
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Grille - <?php echo $grille['nom']; ?></title>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <?php include './includes/nav.php'; ?>

        <h2 class="text-centre">Grille - <?php echo $grille['nom']; ?>.</h2>
        <p class="d-none erreur" id="erreur">Erreur lors de la lecture de la grille</p>
        <p class="d-none success" id="succes">Bravo vous avez résolu la grille</p>
        <div class="d-flex space-between">
            <div class="max-content padding-x-30">

            <?php 
            if(!isAdmin()){
                echo '<div id="chronometre">Temps écoulé : 00:00</div>';
            }
            ?>
                <div class="difficulte">
                    <p> Niveau : <?php echo $grille['niveau_difficulte']; ?></p>
                </div>
                <div id="grille"></div>

                <div class="space-between">
                    <?php
                    if (isAdmin()) {
                    ?>
                    <p class="bouton-rouge" onclick="supprimerGrille()">Supprimer la grille</p>
                    <?php
                    } else {
                    ?>
                    <p class="bouton" onclick="verifierGrille()">Vérifier la grille</p>
                    <?php
                    }
                    ?>
                    
                    <?php
                    if (!isAdmin() && isLoggedIn()) {
                    ?>
                    <p class="bouton" onclick="sauvegarderGrille()">Sauvegarder et quitter</p>
                    <p class="bouton" onclick="solution(this)">Afficher la Solution</p>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <div class="definitions-container">
                <h2>Définitions des mots</h2>
                <h3>HORIZONTALEMENT</h3>
                <div id="definitions_horizontales"></div>
                <h3>VERTICALEMENT</h3>
                <div id="definitions_verticales"></div>
            </div>
        </div>


        <form action="" id="formDonnees" class="d-none">
            <input type="text" name="utilisateur_id" id="utilisateur_id" value="<?php echo $userId; ?>">
            <input type="text" name="grille_id" id="grille_id" value="<?php echo $grille['id']; ?>">
            <input type="text" name="etat_sauvegarde" id="etat_sauvegarde" value="">
        </form>


        <script type="text/javascript" defer>
            let pErreur;
            let pSuccess;
            let lignes = <?php echo $grille['lignes']; ?>;
            let colonnes = <?php echo $grille['colonnes']; ?>;
            <?php
            
            if (!isAdmin()) {
                $solution = $grille['solution'];
            }

            if (isAdmin()) {
                $solution = $grille['solution'];
            }

            ?>;
            let contenuGrille = <?php echo $solution; ?>;
            console.log("contenuGrille : ", contenuGrille);
            let definitions = <?php echo $grille['definitions']; ?>;
            console.log("definitions : ", definitions);
            let etatSauvegarde = <?php echo isset($sauvegardeTrouvee['etat_sauvegarde']) ? $sauvegardeTrouvee['etat_sauvegarde'] : 'null'; ?>;
            console.log("etatSauvegarde : ", etatSauvegarde);

                function validerEntreeGrille(input) {
                // Vérifier que l'entrée est une lettre unique
                if (input.value.length == 1) {
                    // Forcer en majuscule
                    input.value = input.value.toUpperCase();
                    // Ne garder que les lettres
                    input.value = input.value.replace(/[^A-Z]/g, '');
                    if (input.value !== "") {
                        // Marquer la case comme ayant une saisie utilisateur
                        input.dataset.saisie = "true";

                        // Mettre le focus sur l'input suivant
                        const inputs = document.querySelectorAll('.case');
                        const currentIndex = Array.from(inputs).indexOf(input);
                        if (currentIndex < inputs.length - 1) {
                            inputs[currentIndex + 1].focus();
                        }
                    }
                } else {
                    input.value = "";
                    // Supprimer l'attribut si la case est vide
                    input.removeAttribute("data-saisie");
                    input.style.backgroundColor = "";
                    input.style.color = "";
                }
            }


            function creerGrille(readOnly = false) {
                const grille = document.getElementById("grille");
                grille.innerHTML = "";
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
                        if (readOnly) {
                            input.disabled = true;
                        }
                        <?php
                        if (isAdmin()) {
                            echo "input.disabled = true;";
                            echo "input.value = contenuGrille[i+1][j];";
                        }
                        ?>

                        if (contenuGrille[i+1][j] == "#") {
                            input.value = "#";
                            input.style.backgroundColor = "black";
                            input.style.color = "black";
                            input.disabled = true;
                        }
                        if (etatSauvegarde != null) {
                            let caseSauvegarde = etatSauvegarde[i+1][j];
                            if (caseSauvegarde != '_' && caseSauvegarde != '#') {
                                input.value = caseSauvegarde;
                            }
                        }
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

            function creerDefinitions() {
                const definitions_horizontales = document.getElementById("definitions_horizontales");
                definitions_horizontales.innerHTML = "";
                for (let i=1; i<=lignes; i++) {
                    const definition = document.createElement("p");
                    definition.innerHTML = i + " : " + definitions[i].replace(/#/g, " - ");
                    definitions_horizontales.appendChild(definition);
                }

                const definitions_verticales = document.getElementById("definitions_verticales");
                definitions_verticales.innerHTML = "";
                for (let cle of clesDefinitionsLettres) {
                    const definition = document.createElement("p");
                    definition.innerHTML = cle + " : " + definitions[cle].replace(/#/g, " - ");
                    definitions_verticales.appendChild(definition);
                }
            }

            <?php
            if (isAdmin()) {
            ?>
            function supprimerGrille() {
                const formDonnees = new FormData(document.getElementById("formDonnees"));

                fetch('../controleur/supprimerGrille.php', {
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
                        document.getElementById("formDonnees").reset();
                        window.location.href = 'index.php';
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                });
            }
            <?php
            } else {
            ?>
            function verifierGrille() {
                pErreur.classList.add('d-none');
                pSuccess.classList.add('d-none');
                const etatSauvegarde = document.getElementById("etat_sauvegarde");
                let solutionJson = {};
                for (let i=0; i<lignes; i++) {
                    solutionJson[clesDefinitionsChiffres[i]] = "";
                    for (let j=0; j<colonnes; j++) {
                        const contenuCase = document.getElementById("case_" + i + "" + j).value;
                        
                        if (contenuCase == "") {
                            solutionJson[clesDefinitionsChiffres[i]] += "_";
                        } else {
                            solutionJson[clesDefinitionsChiffres[i]] += contenuCase;
                        }
                    }
                }
                etatSauvegarde.value = JSON.stringify(solutionJson);
                console.log('etatSauvegarde : ', etatSauvegarde.value);

                const formDonnees = new FormData(document.getElementById("formDonnees"));

                fetch('../controleur/verifierGrille.php', {
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

                        const verification = JSON.parse(data.resultat);
                        console.log('verification : ', verification);
                        let erreurs = 0;
                        for (let i=0; i<lignes; i++) {
                            for (let j=0; j<colonnes; j++) {
                                const caseGrille = document.getElementById("case_" + i + "" + j);
                                if (verification[i+1][j] == '_') {
                                    caseGrille.style.backgroundColor = "rgba(255, 0, 0, 0.5)";
                                    erreurs++;
                                } else if (verification[i+1][j] == caseGrille.value && caseGrille.value != "#") {
                                    caseGrille.style.backgroundColor = "rgba(0, 255, 0, 0.5)";
                                }
                            }
                        }
                        if (erreurs == 0) {
                            pSuccess.innerText = "Votre grille est correcte !";
                            pSuccess.classList.remove('d-none');
                        }
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                    pErreur.innerText = "Une erreur est survenue lors de la sauvegarde de la grille" + error;
                    pErreur.classList.remove('d-none');
                });
            }
            <?php
            }
            ?>

            <?php
            if (!isAdmin() && isLoggedIn()) {
            ?>
            function sauvegarderGrille() {
                const etatSauvegarde = document.getElementById("etat_sauvegarde");
                let solutionJson = {};
                for (let i=0; i<lignes; i++) {
                    solutionJson[clesDefinitionsChiffres[i]] = "";
                    for (let j=0; j<colonnes; j++) {
                        const contenuCase = document.getElementById("case_" + i + "" + j).value;
                        
                        if (contenuCase == "") {
                            solutionJson[clesDefinitionsChiffres[i]] += "_";
                        } else {
                            solutionJson[clesDefinitionsChiffres[i]] += contenuCase;
                        }
                    }
                }
                etatSauvegarde.value = JSON.stringify(solutionJson);
                console.log('etatSauvegarde : ', etatSauvegarde.value);
                
                const formDonnees = new FormData(document.getElementById("formDonnees"));
                formDonnees.append('action', 'UPDATE');

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
                        document.getElementById("formDonnees").reset();
                        window.location.href = 'index.php';
                    }
                })
                .catch((error) => {
                    console.error('Erreur:', error);
                    pErreur.innerText = "Une erreur est survenue lors de la sauvegarde de la grille" + error;
                    pErreur.classList.remove('d-none');
                });
            }
            <?php
            }
            ?>

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

            function grilleNonValide() {
                const grille = document.getElementById("grille");
                const inputs = grille.querySelectorAll("input");
                for (let input of inputs) {
                    if (input.value == "") return true;
                }
                return false;
            }

            function empecherSoumissionForm() {
                event.preventDefault();
            }

            function submitForm() {
                pErreur.classList.add('d-none');
                event.preventDefault();

                if (!definitionsValides()) {
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
                pErreur = document.getElementById("erreur");
                pSuccess = document.getElementById("succes");
                creerGrille();
                creerDefinitions();
            });


            let solutionsAffichees = false; 

            function solution(buttonElement) {
    // Récupérer toutes les cases de la grille
    for (let i = 0; i < lignes; i++) {
        for (let j = 0; j < colonnes; j++) {
            const caseGrille = document.getElementById("case_" + i + "" + j);
            if (contenuGrille[i + 1][j] !== "#") {
                if (solutionsAffichees) {
                    // Si les solutions sont affichées, les masquer
                    if (!caseGrille.dataset.saisie) {
                        caseGrille.value = ""; 
                        caseGrille.style.backgroundColor = ""; 
                    }
                } else {
                    // Si les solutions ne sont pas affichées, les afficher
                    caseGrille.value = contenuGrille[i + 1][j]; 
                    caseGrille.style.backgroundColor = "rgba(0, 0, 255, 0.3)"; 
                }
            }
        }
    }

    // Basculer l'état des solutions
    solutionsAffichees = !solutionsAffichees;

    // Mettre à jour le texte du bouton
    if (solutionsAffichees) {
        buttonElement.innerText = "Masquer la Solution";
    } else {
        buttonElement.innerText = "Afficher la Solution";
    }
}



            let chronometreActif = false; 
            let tempsEcoule = 0; 
            let intervalId;

            // Fonction pour formater le temps en mm:ss
            function formaterTemps(secs) {
                const minutes = Math.floor(secs / 60).toString().padStart(2, '0');
                const secondes = (secs % 60).toString().padStart(2, '0');
                return `${minutes}:${secondes}`;
            }

            // Fonction pour démarrer le chronomètre
            function demarrerChronometre() {
                if (!chronometreActif) {
                    chronometreActif = true;
                    intervalId = setInterval(() => {
                        tempsEcoule++;
                        document.getElementById('chronometre').innerText = `Temps écoulé : ${formaterTemps(tempsEcoule)}`;
                    }, 1000);
                }
            }

            // Fonction pour arrêter le chronomètre
            function arreterChronometre() {
                if (chronometreActif) {
                    clearInterval(intervalId);
                    chronometreActif = false;
                }
            }

            // Attachez l'événement "click" à la grille pour démarrer le chronomètre
            document.addEventListener('DOMContentLoaded', () => {
                const grille = document.getElementById('grille');
                grille.addEventListener('click', () => {
                    demarrerChronometre();
                });
            });

            // Si vous souhaitez arrêter le chronomètre lors de la vérification ou de la sauvegarde
            document.querySelector('.bouton').addEventListener('click', () => {
                arreterChronometre();
            });


        </script>
    </body>
</html>