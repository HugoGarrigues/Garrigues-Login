<?php

namespace Config;

use PDO;

class BddAccess {

    public static function createPDO($fileName): ?PDO
    {
        $credentials = self::getCredentials($fileName);

        try {
            return new PDO("mysql:host={$credentials->db_host};dbname={$credentials->db_name}", $credentials->db_user, $credentials->db_password, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));
        } catch (\PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            return null;
        }
    }

    private static function getCredentials($fileName) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/../credentials/' . $fileName;
        return json_decode(file_get_contents($path));
    }
}
