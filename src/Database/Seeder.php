<?php

namespace App\Database;

use InvalidArgumentException;
use PDO;

class Seeder
{
    public const SPEC_TABLES = [
        'cpu' => 'cpus',
        'motherboard' => 'motherboards',
        'ram' => 'ram_kits',
        'gpu' => 'gpus',
        'cooler' => 'coolers',
        'case' => 'cases',
        'storage' => 'storage_drives',
        'psu' => 'psus',
    ];

    // Velden die naar een opzoektabel verwijzen: veld in de data => [kolom, tabel]
    private const LOOKUP_FIELDS = [
        'socket' => ['socket_id', 'sockets'],
        'ram_type' => ['ram_type_id', 'ram_types'],
        'form_factor' => ['form_factor_id', 'form_factors'],
        'max_form_factor' => ['max_form_factor_id', 'form_factors'],
    ];

    private const COMMON_FIELDS = ['brand', 'name', 'price_eur', 'price_date', 'power_w'];

    private array $lookupIds = [];

    public function __construct(private PDO $pdo)
    {
    }

    public function run(string $schemaSql, array $lookups, array $parts): void
    {
        $this->pdo->exec($schemaSql);

        $this->pdo->beginTransaction();
        foreach ($lookups as $table => $rows) {
            foreach ($rows as $row) {
                $this->lookupIds[$table][$row['name']] = $this->insert($table, $row);
            }
        }
        foreach ($parts as $category => $items) {
            foreach ($items as $part) {
                $this->insertPart($category, $part);
            }
        }
        $this->pdo->commit();
    }

    private function insertPart(string $category, array $part): void
    {
        $common = array_intersect_key($part, array_flip(self::COMMON_FIELDS));
        $partId = $this->insert('parts', ['category' => $category] + $common);

        $specs = ['part_id' => $partId];
        foreach (array_diff_key($part, $common) as $field => $value) {
            if (isset(self::LOOKUP_FIELDS[$field])) {
                [$column, $table] = self::LOOKUP_FIELDS[$field];
                $specs[$column] = $this->lookupId($table, $value);
            } else {
                $specs[$field] = $value;
            }
        }
        $this->insert(self::SPEC_TABLES[$category], $specs);
    }

    private function lookupId(string $table, string $name): int
    {
        if (!isset($this->lookupIds[$table][$name])) {
            throw new InvalidArgumentException("Onbekende waarde '$name' voor tabel $table");
        }

        return $this->lookupIds[$table][$name];
    }

    // Tabel- en kolomnamen komen uit onze eigen databestanden, nooit van een gebruiker.
    private function insert(string $table, array $row): int
    {
        $row = array_map(fn ($value) => is_bool($value) ? (int) $value : $value, $row);
        $columns = implode(', ', array_keys($row));
        $placeholders = implode(', ', array_fill(0, count($row), '?'));

        $this->pdo->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)")->execute(array_values($row));

        return (int) $this->pdo->lastInsertId();
    }
}
