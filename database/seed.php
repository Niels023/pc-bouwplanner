<?php

use App\Database\Connection;
use App\Database\Seeder;

$config = require __DIR__ . '/../bootstrap.php';

Connection::createDatabase($config['db']);

$parts = require __DIR__ . '/data/parts.php';

(new Seeder(Connection::connect($config['db'])))->run(
    file_get_contents(__DIR__ . '/schema.sql'),
    require __DIR__ . '/data/lookups.php',
    $parts,
);

$count = array_sum(array_map('count', $parts));
echo "Database {$config['db']['name']} is gevuld met $count onderdelen.\n";
