<?php
session_start();

   

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vue/css/style.css">

    <title>Document</title>
</head>
<body>
        <div class="logo">
            <a href="index.php">
                <video autoplay loop muted playsinline>
                    <source src="vue/assets/logo.mp4" type="video/mp4">
                    Votre navigateur ne supporte pas les vidéos HTML5.
                </video>
            </a>
        </div>

        <div class="title">
            <h1>Bienvenue à Tous sur Cruciweb</h1>
        </div>
        

        <article class="article">
            <p>
                Un matin brumeux d'hiver, les cruciverbistes les plus aguerris se réunirent dans un petit café cosy baptisé 
                "Chez les Mots Croisés". Au fond de la salle, une grande affiche annonçait : « Grand Tournoi CruciWeb ». 
                Les participants, armés de crayons, de gommes et de cafés fumants, étaient prêts à relever les défis les plus corsés.
            </p>
            <p>
                Dans un coin sombre de la salle, un vieil homme, barbu et vêtu d’une tunique rappelant l’Antiquité, attirait les regards. 
                On le surnommait "Le Scribe", car il prétendait descendre des scribes égyptiens. Sa présence ajoutait une aura mystérieuse 
                au tournoi. Le Scribe était connu pour ses grilles complexes, inspirées des mythes et des énigmes antiques.
            </p>
            <p>
                L’épreuve finale opposa plusieurs candidats à une grille titanesque : les indices semblaient être des fragments de poèmes antiques. 
                Concentrés, les cruciverbistes traçaient chaque lettre avec précision. Le Scribe, en observateur silencieux, notait chaque mouvement, 
                même les plus subtils. Soudain, une candidate courageuse osa l’interroger : « Maître, est-ce que cet indice pourrait être relié 
                à l’épopée d’Homère ? ».
            </p>
            <p>
                Le Scribe hocha doucement la tête, confirmant son hypothèse. Cette collaboration inattendue illumina la pièce et mena à la résolution finale. 
                Quand la cloche sonna, signalant la fin de l’épreuve, la candidate poussa un soupir de soulagement. Les résultats furent annoncés : 
                elle avait remporté le tournoi !
            </p>
            <p>
                En guise de trophée, elle reçut un manuscrit antique contenant des grilles et énigmes oubliées, transmis par Le Scribe lui-même. 
                De retour chez elle, elle découvrit que chaque grille était un voyage à travers le temps, un hommage aux civilisations qui avaient forgé l’art des mots.
            </p>
</article>

    

    <div>
        <!-- Button de redirection vers vue/index.php -->
        <form action="vue/index.php" method="post" class="forward">
            <button type="submit">Participer à l'aventure</button>
        </form>
    </div>
</body>
</html>
