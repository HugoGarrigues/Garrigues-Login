<?php
// DataManager.php

namespace Services;

use PDO;

class DataManager {
    private PDO $pdo;


    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createUser($username, $email, $hashed_password) {
        // Construction de la requête SQL pour que l'utilsateur puisse etre créer mais aussi pour qu'il ne puisse pas faire d'injection SQL
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";

        // Préparation de la requête
        $sql = $this->pdo->prepare($query);

        // Liaison des paramètres
        $sql->bindValue(':username', $username);
        $sql->bindValue(':email', $email);
        $sql->bindValue(':password', $hashed_password);

        // Exécution de la requête
        $sql->execute();

        // Vérification de l'insertion réussie
        if ($sql->rowCount() > 0) {
            return true; // ou un message de succès si nécessaire
        } else {
            return false; // ou un message d'erreur si nécessaire
        }
    }

    public function updateUserPassword($email, $hashed_password) {
        $sql = $this->pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
        return $sql->execute(['password' => $hashed_password, 'email' => $email]);
    }

    public function emailExists($email) {
        $query = "SELECT COUNT(*) AS count FROM users WHERE email = :email";
        $sql = $this->pdo->prepare($query);
        $sql->bindValue(':email', $email);
        $sql->execute();
        $result = $sql->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'] > 0;
    }

    public static function validatePassword(string $password): bool {
        // Vérification de la longueur du mot de passe
        if (strlen($password) < 8) {
            return false;
        }

        // Vérification qu'il y a au moins un chiffre
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }

        return true;
    }

    public function getUserByEmail($email) {
        // Requête SQL pour sélectionner l'utilisateur par email
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $sql = $this->pdo->prepare($query);
        $sql->bindValue(':email', $email);
        $sql->execute();

        // Récupération de l'utilisateur s'il existe
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

}