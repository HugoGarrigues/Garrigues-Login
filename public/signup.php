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

// Génération d'un token CSRF 
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Génère un token aléatoire sécurisé
}

// Vérification de la méthode de requête HTTP ainsi que du token CSRF
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "Le Token CSRF invalide.";
        exit;
    }

    // Vérification du remplissage des champs du formulaire par l'utilisateur
    if (isset($_POST['username'], $_POST['email'], $_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Appel a la fonction dans DataManager pour vérifier si le mot de passe est valide sinon afficher un message d'erreur et arrête le script
        if (!\Services\DataManager::validatePassword($password)) {
            echo "Le mot de passe doit contenir au moins 8 caractères et inclure au moins un chiffre.";
            exit; 
        }

        // Connexion à la base de données
        $pdo = \Config\BddAccess::createPDO('pwd.json');
        if ($pdo) {
            $dataManager = new \Services\DataManager($pdo);

            // // Appel a la fonction dans DataManager pour vérifier si le mail est déja utilisé sinon cela affiche un message d'erreur et arrête le script
            if ($dataManager->emailExists($email)) {
                echo "Cette adresse email est déjà utilisée. <a href='signin.php'>Connectez-vous ici</a>";
                exit;
            }

            // Hashage du  mot de passe d"e l'utilisateur
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Création de l'utilisateur avec la fonction createUser de DataManager 
            $success = $dataManager->createUser($username, $email, $hashed_password);

            // Si l'inscription est réussie, créer la session utilisateur et rediriger vers signedin.php
            if ($success) {
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;

                header('Location: signedin.php');
                exit;
            } else {
                echo "Erreur lors de l'inscription.";
            }
        } else {
            echo "Erreur de connexion à la base de données.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>

<!-- Ajout d'un Formulaire en html pour que l'utilisateur puisse s'inscrire  -->
<form action="signup.php" method="POST">
    <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
    <input type="email" name="email" placeholder="Adresse email" required><br>
    <input type="password" name="password" placeholder="Mot de passe" required><br>
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <button type="submit">S'inscrire</button>
</form>

<p>Déjà inscrit ? <a href="signin.php">Connectez-vous ici</a></p>
