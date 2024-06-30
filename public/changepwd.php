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
    if (isset($_POST['old_password'], $_POST['new_password'], $_POST['confirm_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Appel a la fonction dans DataManager pour vérifier si le mot de passe est valide sinon afficher un message d'erreur et arrête le script
        if (!\Services\DataManager::validatePassword($new_password)) {
            echo "Le mot de passe doit contenir au moins 8 caractères et inclure au moins un chiffre.";
            exit; 
        }

        // Connexion à la base de données
        $pdo = \Config\BddAccess::createPDO('pwd.json');
        if ($pdo) {
            $dataManager = new \Services\DataManager($pdo);

            // Récupération de l'email de l'utilisateur connecté
            $email = $_SESSION['email'];

            // Vérification de l'ancien mot de passe
            $user = $dataManager->getUserByEmail($email);

            // Si le mot de passe est correct, mettre à jour le mot de passe de l'utilisateur
            if ($user && password_verify($old_password, $user['password'])) {
                // Vérification des 2 nouveaux mots de passe
                if ($new_password === $confirm_password) {

                    // Hashage du nouveau mot de passe
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Actualisation du mot de passe de l'utilisateu dans la base de données
                    $success = $dataManager->updateUserPassword($email, $hashed_password);

                    // Affichage d'un message de réussite ou liste d'erreurs
                    if ($success) {
                        echo "Mot de passe mis à jour avec succès !";
 
                    } else {
                        echo "Erreur lors de la mise à jour du mot de passe.";
                    }
                } else {
                    echo "Les nouveaux mots de passe ne correspondent pas.";
                }
            } else {
                echo "Ancien mot de passe incorrect.";
            }
        } else {
            echo "Erreur de connexion à la base de données.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
}
?>

<!-- Ajout d'un Formulaire en html pour que l'utilisateur change de mot de passe  -->
<form action="changepwd.php" method="POST">
    <input type="password" name="old_password" placeholder="Ancien mot de passe" required><br>
    <input type="password" name="new_password" placeholder="Nouveau mot de passe" required><br>
    <input type="password" name="confirm_password" placeholder="Confirmer le nouveau mot de passe" required><br>
    <button type="submit">Changer le mot de passe</button>
</form>