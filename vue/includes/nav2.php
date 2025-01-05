<nav>
    <a href="index.php">Accueil</a>
    <?php if (!isLoggedIn()) { ?>
        <a href="connexion.php">Connexion</a>
        <a href="inscription.php">Inscription</a>
    <?php } ?>
</nav>