<?php

namespace Tests\Database;

use App\Model\Category;
use App\Model\Cooler;
use App\Model\Cpu;
use App\Model\Gpu;
use App\Model\Motherboard;
use App\Model\Part;
use App\Model\PcCase;
use App\Model\Psu;
use App\Model\RamKit;
use App\Model\StorageDrive;
use App\Repository\PartFilter;
use App\Repository\PartRepository;
use PHPUnit\Framework\TestCase;
use Tests\Support\TestDatabase;

class PartRepositoryTest extends TestCase
{
    private PartRepository $parts;

    protected function setUp(): void
    {
        $this->parts = new PartRepository(TestDatabase::seeded());
    }

    public function testEveryCategoryBecomesTheRightClass(): void
    {
        $expected = [
            Category::Cpu->value => Cpu::class,
            Category::Motherboard->value => Motherboard::class,
            Category::Ram->value => RamKit::class,
            Category::Gpu->value => Gpu::class,
            Category::Cooler->value => Cooler::class,
            Category::PcCase->value => PcCase::class,
            Category::Storage->value => StorageDrive::class,
            Category::Psu->value => Psu::class,
        ];

        foreach ($this->parts->findByFilter(new PartFilter()) as $part) {
            $this->assertInstanceOf($expected[$part->category()->value], $part);
        }
    }

    public function testCpuHasAllItsFields(): void
    {
        $cpu = $this->partNamed('Ryzen 5 7600 Boxed');

        $this->assertInstanceOf(Cpu::class, $cpu);
        $this->assertSame('AMD', $cpu->brand);
        $this->assertSame(17682, $cpu->priceCents);
        $this->assertSame('2026-10-28', $cpu->priceDate->format('Y-m-d'));
        $this->assertSame(88, $cpu->powerW);
        $this->assertSame('AM5', $cpu->socket);
        $this->assertSame(5.1, $cpu->boostClockGhz);
        $this->assertTrue($cpu->hasIntegratedGraphics);
    }

    public function testBoardAndCaseGetTheirFormFactor(): void
    {
        $board = $this->partNamed('B850M DS3H');
        $case = $this->partNamed('MasterBox NR200P V3');

        $this->assertSame('Micro-ATX', $board->formFactor->name);
        $this->assertSame('Mini-ITX', $case->maxFormFactor->name);
        $this->assertFalse($board->formFactor->fitsIn($case->maxFormFactor));
    }

    public function testRamAndStorageGetTheirOwnCapacity(): void
    {
        $this->assertSame(32, $this->partNamed('FURY Beast 32GB (2x16GB) DDR4-3200')->capacityGb);
        $this->assertSame(2000, $this->partNamed('990 PRO 2TB')->capacityGb);
    }

    public function testFindByIdReturnsNullForUnknownId(): void
    {
        $this->assertNull($this->parts->findById(999999));
    }

    public function testFindByIdsWithEmptyListGivesEmptyList(): void
    {
        $this->assertSame([], $this->parts->findByIds([]));
    }

    public function testFindByIdsReturnsOnlyExistingParts(): void
    {
        $id = $this->partNamed('990 PRO 2TB')->id;

        $parts = $this->parts->findByIds([$id, 999999]);

        $this->assertCount(1, $parts);
        $this->assertSame($id, $parts[0]->id);
    }

    public function testFiltersCanBeCombined(): void
    {
        $filter = new PartFilter(category: Category::Cpu, brand: 'AMD', maxPriceCents: 19000, socket: 'AM5');

        $names = array_map(fn (Part $part) => $part->name, $this->parts->findByFilter($filter));

        $this->assertSame(['Ryzen 5 7600 Boxed', 'Ryzen 5 9600X Boxed'], $names);
    }

    public function testMaxPriceIsInclusive(): void
    {
        $filter = new PartFilter(category: Category::Cpu, maxPriceCents: 12496);

        $this->assertCount(1, $this->parts->findByFilter($filter));
    }

    public function testFormFactorFilterFindsBoardsAndCases(): void
    {
        $parts = $this->parts->findByFilter(new PartFilter(formFactor: 'Mini-ITX'));

        $categories = array_map(fn (Part $part) => $part->category(), $parts);

        $this->assertContains(Category::Motherboard, $categories);
        $this->assertContains(Category::PcCase, $categories);
    }

    public function testPartsAreSortedByCategoryAndPrice(): void
    {
        $prices = array_map(
            fn (Part $part) => $part->priceCents,
            $this->parts->findByFilter(new PartFilter(category: Category::Gpu)),
        );
        $sorted = $prices;
        sort($sorted);

        $this->assertSame($sorted, $prices);
    }

    private function partNamed(string $name): Part
    {
        foreach ($this->parts->findByFilter(new PartFilter()) as $part) {
            if ($part->name === $name) {
                return $part;
            }
        }

        $this->fail("Onderdeel $name staat niet in de data");
    }
}
