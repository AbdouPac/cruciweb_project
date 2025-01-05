<nav>
    <a href="index.php">Accueil</a>
    <?php if (isAdmin()) { ?>
        <a href="gererUtilisateurs.php">Gérer les utilisateurs</a>
    <?php } ?>
    <?php if (isInscrit()) { ?>
        <a href="creerGrille.php">Créer une grille</a>
        <a href="sauvegardes.php">Gérer les sauvegardes</a>
    <?php } ?>
    <?php if (isLoggedIn()) { ?>
        <span><?php echo $_SESSION['user_email']; ?></span>
        <a href="deconnexion.php" class="btn-rouge">Déconnexion</a>
    <?php } else { ?>
        <a href="connexion.php">Connexion</a>
        <a href="inscription.php">Inscription</a>
    <?php } ?>
</nav>