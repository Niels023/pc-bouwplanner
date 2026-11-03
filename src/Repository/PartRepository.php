<?php

namespace App\Repository;

use App\Model\Category;
use App\Model\Cooler;
use App\Model\Cpu;
use App\Model\FormFactor;
use App\Model\Gpu;
use App\Model\Motherboard;
use App\Model\Part;
use App\Model\PcCase;
use App\Model\Psu;
use App\Model\RamKit;
use App\Model\StorageDrive;
use DateTimeImmutable;
use PDO;

class PartRepository
{
    // Vaste lijst, dus er komt nooit tekst van een gebruiker in de ORDER BY.
    private const ORDER_BY = [
        'price_asc' => 'category, price_eur, name',
        'price_desc' => 'category, price_eur DESC, name',
        'name' => 'category, brand, name',
    ];

    public function __construct(private PDO $pdo)
    {
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

        return $this->fetchParts($sql . ' ORDER BY ' . self::ORDER_BY[$filter->sort], $values);
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

        return array_map($this->fromRow(...), $statement->fetchAll());
    }

    // Maakt van een rij uit de view part_details het juiste object.
    private function fromRow(array $row): Part
    {
        $common = [
            'id' => $row['id'],
            'brand' => $row['brand'],
            'name' => $row['name'],
            'priceCents' => (int) round($row['price_eur'] * 100),
            'priceDate' => new DateTimeImmutable($row['price_date']),
            'powerW' => $row['power_w'],
        ];

        return match (Category::from($row['category'])) {
            Category::Cpu => $this->cpu($common, $row),
            Category::Motherboard => $this->motherboard($common, $row),
            Category::Ram => $this->ramKit($common, $row),
            Category::Gpu => $this->gpu($common, $row),
            Category::Cooler => new Cooler(
                ...$common,
                heightMm: $row['height_mm'],
                radiatorMm: $row['radiator_mm'],
                sockets: explode(',', $row['cooler_sockets'] ?? ''),
            ),
            Category::PcCase => $this->pcCase($common, $row),
            Category::Storage => $this->storageDrive($common, $row),
            Category::Psu => $this->psu($common, $row),
        };
    }

    private function cpu(array $common, array $row): Cpu
    {
        return new Cpu(
            ...$common,
            socket: $row['socket'],
            cores: $row['cores'],
            threads: $row['threads'],
            boostClockGhz: (float) $row['boost_clock_ghz'],
            hasIntegratedGraphics: (bool) $row['has_integrated_graphics'],
        );
    }

    private function motherboard(array $common, array $row): Motherboard
    {
        return new Motherboard(
            ...$common,
            socket: $row['socket'],
            ramType: $row['ram_type'],
            formFactor: new FormFactor($row['form_factor'], $row['form_factor_rank']),
            chipset: $row['chipset'],
            ramSlots: $row['ram_slots'],
        );
    }

    private function ramKit(array $common, array $row): RamKit
    {
        return new RamKit(
            ...$common,
            ramType: $row['ram_type'],
            capacityGb: $row['capacity_gb'],
            moduleCount: $row['module_count'],
            speedMts: $row['speed_mts'],
        );
    }

    private function gpu(array $common, array $row): Gpu
    {
        return new Gpu(
            ...$common,
            chip: $row['chip'],
            vramGb: $row['vram_gb'],
            lengthMm: $row['length_mm'],
            fps1080p: $row['fps_1080p'],
            fps1440p: $row['fps_1440p'],
            fps4k: $row['fps_4k'],
        );
    }

    private function pcCase(array $common, array $row): PcCase
    {
        return new PcCase(
            ...$common,
            maxFormFactor: new FormFactor($row['max_form_factor'], $row['max_form_factor_rank']),
            maxGpuLengthMm: $row['max_gpu_length_mm'],
            maxCoolerHeightMm: $row['max_cooler_height_mm'],
            maxRadiatorMm: $row['max_radiator_mm'],
        );
    }

    private function storageDrive(array $common, array $row): StorageDrive
    {
        return new StorageDrive(...$common, type: $row['storage_type'], capacityGb: $row['capacity_gb']);
    }

    private function psu(array $common, array $row): Psu
    {
        return new Psu(...$common, wattageW: $row['wattage_w'], efficiencyRating: $row['efficiency_rating']);
    }
}
