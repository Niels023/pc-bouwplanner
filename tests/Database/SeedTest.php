<?php

namespace Tests\Database;

use App\Database\Connection;
use App\Database\Seeder;
use PDO;
use PHPUnit\Framework\TestCase;

class SeedTest extends TestCase
{
    private static PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        $config = require __DIR__ . '/../../config/app.php';
        Connection::createDatabase($config['db']);
        self::$pdo = Connection::connect($config['db']);

        (new Seeder(self::$pdo))->run(
            file_get_contents(__DIR__ . '/../../database/schema.sql'),
            require __DIR__ . '/../../database/data/lookups.php',
            require __DIR__ . '/../../database/data/parts.php',
        );
    }

    public function testDatabaseHasBetween40And60Parts(): void
    {
        $count = self::$pdo->query('SELECT COUNT(*) FROM parts')->fetchColumn();

        $this->assertGreaterThanOrEqual(40, $count);
        $this->assertLessThanOrEqual(60, $count);
    }

    public function testEveryCategoryHasAtLeastFourParts(): void
    {
        $counts = self::$pdo->query('SELECT category, COUNT(*) FROM parts GROUP BY category')
            ->fetchAll(PDO::FETCH_KEY_PAIR);

        $this->assertCount(8, $counts);
        foreach ($counts as $category => $count) {
            $this->assertGreaterThanOrEqual(4, $count, "Te weinig onderdelen in categorie $category");
        }
    }

    public function testEveryPartHasSpecificationsForItsCategory(): void
    {
        foreach (Seeder::SPEC_TABLES as $category => $table) {
            $statement = self::$pdo->prepare(
                "SELECT COUNT(*) FROM parts p LEFT JOIN $table s ON s.part_id = p.id
                 WHERE p.category = ? AND s.part_id IS NULL"
            );
            $statement->execute([$category]);

            $this->assertSame(0, $statement->fetchColumn(), "Onderdelen zonder specificaties in $table");
        }
    }

    public function testEveryPartHasPriceAndPriceDate(): void
    {
        $count = self::$pdo->query('SELECT COUNT(*) FROM parts WHERE price_eur <= 0 OR price_date IS NULL')
            ->fetchColumn();

        $this->assertSame(0, $count);
    }
}
