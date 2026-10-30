<?php

namespace App\Repository;

use App\Model\Part;
use PDO;

class PartRepository
{
    private PartMapper $mapper;

    public function __construct(private PDO $pdo)
    {
        $this->mapper = new PartMapper();
    }

    public function findById(int $id): ?Part
    {
        return $this->findByIds([$id])[0] ?? null;
    }

    /**
     * @param int[] $ids
     * @return Part[]
     */
    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));

        return $this->fetchParts("SELECT * FROM part_details WHERE id IN ($placeholders)", array_values($ids));
    }

    /** @return Part[] */
    public function findByFilter(PartFilter $filter): array
    {
        [$conditions, $values] = $this->conditionsFor($filter);

        $sql = 'SELECT * FROM part_details';
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        return $this->fetchParts($sql . ' ORDER BY category, price_eur, name', $values);
    }

    /** @return array{0: string[], 1: array<int, mixed>} */
    private function conditionsFor(PartFilter $filter): array
    {
        $conditions = [];
        $values = [];

        if ($filter->category !== null) {
            $conditions[] = 'category = ?';
            $values[] = $filter->category->value;
        }
        if ($filter->brand !== null) {
            $conditions[] = 'brand = ?';
            $values[] = $filter->brand;
        }
        if ($filter->maxPriceCents !== null) {
            $conditions[] = 'price_eur <= ?';
            $values[] = $filter->maxPriceCents / 100;
        }
        if ($filter->socket !== null) {
            $conditions[] = 'socket = ?';
            $values[] = $filter->socket;
        }
        if ($filter->ramType !== null) {
            $conditions[] = 'ram_type = ?';
            $values[] = $filter->ramType;
        }
        if ($filter->formFactor !== null) {
            $conditions[] = '(form_factor = ? OR max_form_factor = ?)';
            $values[] = $filter->formFactor;
            $values[] = $filter->formFactor;
        }

        return [$conditions, $values];
    }

    /** @return Part[] */
    private function fetchParts(string $sql, array $values): array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($values);

        return array_map($this->mapper->fromRow(...), $statement->fetchAll());
    }
}
