<?php

// Démarrage de la session
session_start();

// Gestion du timeout de session et redirection vers la page de connexion si la session est expirée
$session_timeout = 1800; 
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Expire la session
    session_unset();
    session_destroy();
    header('Location: signin.php'); 
    exit;
}

// Gestion de la dernière activité de l'utilisateur 
$_SESSION['last_activity'] = time();


// Vérification de si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: signin.php');
    exit();
}

// Création d'une variable pour stocker le nom d'utilisateur
$username = $_SESSION['username'];
?>

<!-- Création d'une page HTML pour que l'utilisateur puisse etre rediriger vers plusieurs choix  -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page connectée</title>
</head>
<body>
    <h1>Bienvenue, <?= htmlspecialchars($username) ?></h1>
    <p><a href="changepwd.php">Changer le mot de passe</a></p>
    <p><a href="index.php?action=signout">Se déconnecter</a></p>
</body>
</html>
