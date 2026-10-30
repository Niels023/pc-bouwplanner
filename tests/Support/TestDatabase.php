<?php

namespace Tests\Support;

use App\Database\Connection;
use App\Database\Seeder;
use PDO;

// Vult de testdatabase. Eén keer per testrun is genoeg, dat scheelt tijd.
class TestDatabase
{
    private static ?PDO $pdo = null;

    public static function seeded(): PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../../config/app.php';
            Connection::createDatabase($config['db']);
            self::$pdo = Connection::connect($config['db']);

            (new Seeder(self::$pdo))->run(
                file_get_contents(__DIR__ . '/../../database/schema.sql'),
                require __DIR__ . '/../../database/data/lookups.php',
                require __DIR__ . '/../../database/data/parts.php',
            );
        }

        return self::$pdo;
    }
}
