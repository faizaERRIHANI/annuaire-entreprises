<?php
declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_PORT', '3307'); // mets 3306 si ton MySQL utilise 3306
define('DB_NAME', 'annuaire_entreprises');
define('DB_USER', 'root');
define('DB_PASS', '');

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    return $pdo;
}