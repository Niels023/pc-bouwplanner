<?php

namespace Tests\Api;

use App\Http\Request;
use App\Http\Response;

class BuildCheckApiTest extends ApiTestCase
{
    private const COMPLETE_BUILD = [
        'Ryzen 5 7600 Boxed',
        'B850 GAMING PLUS WIFI',
        'FURY Beast 16GB (2x8GB) DDR5-5600',
        'GeForce RTX 5060 8G VENTUS 2X OC',
        'AK400',
        'Lancool 216',
        'NV3 M.2 2280 1TB',
        'CX550 (2023)',
    ];

    public function testEmptyListGivesIncomplete(): void
    {
        $response = $this->check([]);

        $this->assertSame(200, $response->status);
        $data = $this->data($response);
        $this->assertSame('incomplete', $data['overall_status']);
        $this->assertSame(0, $data['total_price_eur']);
        $this->assertCount(7, $data['results']);
    }

    public function testCompleteBuildThatFitsIsOk(): void
    {
        $data = $this->data($this->check(self::COMPLETE_BUILD));

        $this->assertSame('ok', $data['overall_status']);
        $this->assertSame(['ok'], array_values(array_unique(array_column($data['results'], 'status'))));
    }

    public function testTotalsAreCalculated(): void
    {
        $data = $this->data($this->check(['Ryzen 5 5600 Boxed', 'B550M Pro4']));

        $this->assertSame(220.44, $data['total_price_eur']);
        $this->assertSame(128, $data['estimated_power_w']);
    }

    public function testWrongSocketGivesErrorWithMessage(): void
    {
        $data = $this->data($this->check(['Ryzen 5 5600 Boxed', 'B850M DS3H']));

        $this->assertSame('error', $data['overall_status']);
        $result = $this->resultFor($data, 'socket');
        $this->assertSame('error', $result['status']);
        $this->assertSame('De processor heeft socket AM4, maar het moederbord heeft socket AM5.', $result['message']);
    }

    public function testMissingPartGivesNotChecked(): void
    {
        $data = $this->data($this->check(['Ryzen 5 5600 Boxed']));

        $result = $this->resultFor($data, 'socket');
        $this->assertSame('not_checked', $result['status']);
        $this->assertSame('Kies eerst een moederbord.', $result['message']);
    }

    public function testRealPartsOnTheEdgeOfFitting(): void
    {
        $astral = 'ROG Astral GeForce RTX 5080 16GB GDDR7 OC Edition';

        $cooler = $this->data($this->check(['NH-L12S', 'MasterBox NR200P V3']));
        $tooLong = $this->data($this->check([$astral, 'North TG Clear Charcoal Black']));
        $justFits = $this->data($this->check([$astral, 'MasterBox Q300L V2']));

        $this->assertSame('ok', $this->resultFor($cooler, 'cooler_height')['status']);
        $this->assertSame('error', $this->resultFor($tooLong, 'gpu_length')['status']);
        $this->assertSame('ok', $this->resultFor($justFits, 'gpu_length')['status']);
    }

    public function testHighEndBuildOnSmallPsuGivesWarning(): void
    {
        $parts = [
            'Ryzen 7 9800X3D Boxed',
            'MAG B860 TOMAHAWK WIFI',
            'Vengeance 32GB (2x16GB) DDR5-6000 CL30',
            'ROG Astral GeForce RTX 5080 16GB GDDR7 OC Edition',
            'NH-D15 G2',
            'Lancool 216',
            'WD_BLACK SN850X 1TB',
            'FOCUS GX ATX 3 (2024) 750W',
        ];

        $data = $this->data($this->check($parts));

        $this->assertSame(583, $data['estimated_power_w']);
        $this->assertSame('warning', $this->resultFor($data, 'psu_wattage')['status']);
    }

    public function testBuildWithoutGpuCanBeOk(): void
    {
        $parts = self::COMPLETE_BUILD;
        unset($parts[3]);

        $data = $this->data($this->check(array_values($parts)));

        $this->assertSame('ok', $data['overall_status']);
    }

    public function testBuildWithoutGpuOrIntegratedGraphicsIsAnError(): void
    {
        $parts = [
            'Ryzen 5 5600 Boxed',
            'B550M Pro4',
            'FURY Beast 16GB (2x8GB) DDR4-3200',
            'AK400',
            'Lancool 216',
            'NV3 M.2 2280 1TB',
            'CX550 (2023)',
        ];

        $data = $this->data($this->check($parts));

        $this->assertSame('error', $this->resultFor($data, 'graphics')['status']);
    }

    public function testInvalidJsonGivesBadRequest(): void
    {
        $response = $this->handle(new Request('POST', '/api/builds/check', [], '{dit is geen json'));

        $this->assertError($response, 400, 'invalid_json');
    }

    public function testMissingPartIdsGivesError(): void
    {
        $this->assertError($this->postJson('/api/builds/check', []), 422, 'invalid_part_ids');
    }

    public function testPartIdsMustBeAListOfPositiveNumbers(): void
    {
        foreach ([5, 'abc', [1, 'x'], [0], [-1], [1.5], ['a' => 1]] as $partIds) {
            $response = $this->postJson('/api/builds/check', ['part_ids' => $partIds]);

            $this->assertError($response, 422, 'invalid_part_ids');
        }
    }

    public function testUnknownPartGivesErrorWithId(): void
    {
        $response = $this->postJson('/api/builds/check', ['part_ids' => [999999]]);

        $this->assertError($response, 422, 'unknown_part');
        $this->assertStringContainsString('999999', $this->data($response)['error']['message']);
    }

    public function testTwoPartsInSameCategoryGivesError(): void
    {
        $response = $this->check(['Ryzen 5 5600 Boxed', 'Ryzen 5 7600 Boxed']);

        $this->assertError($response, 422, 'duplicate_category');
    }

    private function check(array $partNames): Response
    {
        $ids = array_map(fn (string $name) => $this->partId($name), $partNames);

        return $this->postJson('/api/builds/check', ['part_ids' => $ids]);
    }

    private function resultFor(array $data, string $rule): array
    {
        foreach ($data['results'] as $result) {
            if ($result['rule'] === $rule) {
                return $result;
            }
        }

        $this->fail("Geen resultaat voor regel $rule");
    }
}
