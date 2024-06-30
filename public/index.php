<?php

// Démarrage de la session 
session_start();

// Gestion de la session et redirection 
if (isset($_GET['action']) && $_GET['action'] === 'signout') {
    session_unset();
    session_destroy();
    header('Location: signin.php');
    exit();
}
?>

<!-- Création d'une page HTML pour rediriger l'utilisateur  -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
</head>
<body>
    <h1>Page d'accueil</h1>
    <?php if (isset($_SESSION['username'])) : ?>
        <p>Connecté en tant que <?= htmlspecialchars($_SESSION['username']) ?></p>
        <p><a href="signedin.php">Accéder à la page connectée</a></p>
        <p><a href="changepwd.php">Changer le mot de passe</a></p>
        <p><a href="index.php?action=signout">Se déconnecter</a></p>
    <?php else : ?>
        <p><a href="signin.php">Se connecter</a></p>
        <p><a href="signup.php">S'inscrire</a></p>
    <?php endif; ?>
</body>
</html>
