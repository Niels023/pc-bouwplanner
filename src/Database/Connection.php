<?php

namespace App\Database;

use PDO;

class Connection
{
    public static function connect(array $db): PDO
    {
        $dsn = "mysql:host={$db['host']};port={$db['port']};dbname={$db['name']};charset=utf8mb4";

        return new PDO($dsn, $db['user'], $db['password'], [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Echte prepared statements, en getallen komen terug als int in plaats van string
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function createDatabase(array $db): void
    {
        $pdo = new PDO("mysql:host={$db['host']};port={$db['port']};charset=utf8mb4", $db['user'], $db['password']);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
}
