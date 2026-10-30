<?php

namespace Tests\Api;

use App\Http\Request;

class PartApiTest extends ApiTestCase
{
    public function testListGivesAllPartsAsJson(): void
    {
        $response = $this->get('/api/parts');

        $this->assertSame(200, $response->status);
        $this->assertStringContainsString('application/json', $response->headers['Content-Type']);
        $data = $this->data($response);
        $this->assertGreaterThanOrEqual(40, $data['count']);
        $this->assertCount($data['count'], $data['parts']);
    }

    public function testPartHasPriceDateAndSpecs(): void
    {
        $id = $this->partId('Ryzen 5 7600 Boxed');

        $data = $this->data($this->get("/api/parts/$id"));

        $this->assertSame('cpu', $data['category']);
        $this->assertSame('AMD', $data['brand']);
        $this->assertSame(176.82, $data['price_eur']);
        $this->assertSame('2026-10-28', $data['price_date']);
        $this->assertSame('AM5', $data['specs']['socket']);
    }

    public function testFilterByCategory(): void
    {
        $data = $this->data($this->get('/api/parts', ['category' => 'gpu']));

        $this->assertGreaterThan(0, $data['count']);
        foreach ($data['parts'] as $part) {
            $this->assertSame('gpu', $part['category']);
        }
    }

    public function testUnknownCategoryGivesError(): void
    {
        $response = $this->get('/api/parts', ['category' => 'toetsenbord']);

        $this->assertError($response, 422, 'invalid_category');
        $this->assertStringContainsString('toetsenbord', $this->data($response)['error']['message']);
    }

    public function testFilterBySocketFindsCpusAndBoards(): void
    {
        $data = $this->data($this->get('/api/parts', ['socket' => 'AM4']));

        $categories = array_column($data['parts'], 'category');
        $this->assertContains('cpu', $categories);
        $this->assertContains('motherboard', $categories);
        foreach ($data['parts'] as $part) {
            $this->assertSame('AM4', $part['specs']['socket']);
        }
    }

    public function testFilterByRamType(): void
    {
        $data = $this->data($this->get('/api/parts', ['ram_type' => 'DDR4', 'category' => 'ram']));

        $this->assertGreaterThan(0, $data['count']);
        foreach ($data['parts'] as $part) {
            $this->assertSame('DDR4', $part['specs']['ram_type']);
        }
    }

    public function testFilterByFormFactorFindsBoardsAndCases(): void
    {
        $data = $this->data($this->get('/api/parts', ['form_factor' => 'Mini-ITX']));

        $categories = array_column($data['parts'], 'category');
        $this->assertContains('motherboard', $categories);
        $this->assertContains('case', $categories);
    }

    public function testFilterByBrand(): void
    {
        $data = $this->data($this->get('/api/parts', ['brand' => 'Noctua']));

        $this->assertSame(['Noctua'], array_values(array_unique(array_column($data['parts'], 'brand'))));
    }

    public function testMaxPriceInEuros(): void
    {
        $data = $this->data($this->get('/api/parts', ['category' => 'cpu', 'max_price' => '176.82']));

        $this->assertSame(176.82, max(array_column($data['parts'], 'price_eur')));
    }

    public function testInvalidMaxPriceGivesError(): void
    {
        foreach (['abc', '0', '-10'] as $value) {
            $this->assertError($this->get('/api/parts', ['max_price' => $value]), 422, 'invalid_max_price');
        }
    }

    public function testFiltersCanBeCombined(): void
    {
        $query = ['category' => 'cpu', 'brand' => 'AMD', 'socket' => 'AM5', 'max_price' => '190'];

        $data = $this->data($this->get('/api/parts', $query));

        $this->assertSame(2, $data['count']);
    }

    public function testSortByPriceHighToLow(): void
    {
        $data = $this->data($this->get('/api/parts', ['category' => 'gpu', 'sort' => 'price_desc']));

        $prices = array_column($data['parts'], 'price_eur');
        $sorted = $prices;
        rsort($sorted);
        $this->assertSame($sorted, $prices);
    }

    public function testSortByName(): void
    {
        $data = $this->data($this->get('/api/parts', ['category' => 'cooler', 'sort' => 'name']));

        $this->assertSame('ARCTIC', $data['parts'][0]['brand']);
        $this->assertSame('Thermalright', $data['parts'][count($data['parts']) - 1]['brand']);
    }

    public function testDefaultSortIsPriceLowToHigh(): void
    {
        $default = $this->data($this->get('/api/parts', ['category' => 'gpu']));
        $sorted = $this->data($this->get('/api/parts', ['category' => 'gpu', 'sort' => 'price_asc']));

        $this->assertSame($sorted['parts'], $default['parts']);
    }

    public function testUnknownSortGivesError(): void
    {
        $response = $this->get('/api/parts', ['sort' => 'price; DROP TABLE parts']);

        $this->assertError($response, 422, 'invalid_sort');
    }

    public function testNoResultsGivesEmptyList(): void
    {
        $response = $this->get('/api/parts', ['brand' => 'Bestaat niet']);

        $this->assertSame(200, $response->status);
        $this->assertSame(['count' => 0, 'parts' => []], $this->data($response));
    }

    public function testEmptyFilterIsIgnored(): void
    {
        $all = $this->data($this->get('/api/parts'));

        $data = $this->data($this->get('/api/parts', ['brand' => '', 'category' => '']));

        $this->assertSame($all['count'], $data['count']);
    }

    public function testFilterAsArrayIsIgnored(): void
    {
        $response = $this->get('/api/parts', ['category' => ['cpu', 'gpu']]);

        $this->assertSame(200, $response->status);
    }

    public function testSqlInjectionInFilterDoesNothing(): void
    {
        $data = $this->data($this->get('/api/parts', ['brand' => "' OR '1'='1"]));

        $this->assertSame(0, $data['count']);
    }

    public function testUnknownPartGivesNotFound(): void
    {
        $this->assertError($this->get('/api/parts/999999'), 404, 'part_not_found');
    }

    public function testIdThatIsNotANumberGivesNotFound(): void
    {
        $this->assertError($this->get('/api/parts/abc'), 404, 'part_not_found');
    }

    public function testUnknownPathGivesNotFound(): void
    {
        $this->assertError($this->get('/api/bestaat-niet'), 404, 'not_found');
    }

    public function testWrongMethodGivesMethodNotAllowed(): void
    {
        $response = $this->handle(new Request('DELETE', '/api/parts'));

        $this->assertError($response, 405, 'method_not_allowed');
    }
}
