<?php

// Inclusion des fichiers nécessaires pour la connexion à la base de données et la gestion des données
require_once $_SERVER['DOCUMENT_ROOT'] . '/Garrigues-Login/src/Config/BddAccess.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Garrigues-Login/src/Services/DataManager.php';

// Démarrage de la session
session_start();

// Gestion du timeout de session et redirection vers la page de connexion si la session est expirée
$session_timeout = 1800; 
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header('Location: signin.php'); 
    exit;
}

// Gestion de la dernière activité de l'utilisateur 
$_SESSION['last_activity'] = time();

// Vérification de la méthode de requête HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Vérification des données du formulaire
    if (isset($_POST['email'], $_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Connexion à la base de données
        $pdo = \Config\BddAccess::createPDO('pwd.json');
        if ($pdo) {
            $dataManager = new \Services\DataManager($pdo);

            // Vérifucation du nombre de tentatives de connexion
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }

            // Création des variables pour le nombre de tentatives et le temps de blocage
            $max_attempts = 5; 
            $lockout_time = 10; 

            // Vérifier si le compte est déjà verrouillé 
            if ($_SESSION['login_attempts'] >= $max_attempts) {
                if (isset($_SESSION['last_login_attempt']) && (time() - $_SESSION['last_login_attempt']) < $lockout_time) {
                    $time_left = $lockout_time - (time() - $_SESSION['last_login_attempt']);
                    echo "Trop de tentatives de connexion. Veuillez réessayer dans $time_left secondes.";
                    exit; 
                }
            }

            // Création d'une variable pour stocker les infos de l'utilisateur
            $user = $dataManager->getUserByEmail($email);

            // Si connecter alors connecter l'utilisateur et reset le nombre de tentatives de login
            if ($user && password_verify($password, $user['password'])) {

                $_SESSION['login_attempts'] = 0;
                unset($_SESSION['last_login_attempt']);

                // Si les informations sont correctes, récuperer et afficher les informations de l'utilisateur dans la session
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Redirection vers la page signedin.php
                header('Location: signedin.php');
                exit;
            } else {
                // Gestion du compteur de login et du temps de blocage
                $_SESSION['login_attempts']++;
                $_SESSION['last_login_attempt'] = time();
                echo "Identifiants incorrects.";

                // Envoie d'un msg si le nombre de tentatives de connexion est atteint
                if ($_SESSION['login_attempts'] >= $max_attempts) {
                    echo " Trop de tentatives de connexion. Veuillez réessayer dans $lockout_time secondes.";
                }
            }
        } else {
            echo "Erreur de connexion à la base de données.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>

<!-- Ajout d'un Formulaire en html pour que l'utilisateur puisse se connecter  -->
<form action="signin.php" method="POST">
    <input type="email" name="email" placeholder="Adresse email" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>
    <button type="submit">Se connecter</button>
</form>

<p>Pas encore inscrit ? <a href="signup.php">S'inscrire ici</a></p>
