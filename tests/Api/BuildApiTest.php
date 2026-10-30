<?php

namespace Tests\Api;

use App\Http\Response;

class BuildApiTest extends ApiTestCase
{
    public function testSavedBuildGetsShareCodeAndUrl(): void
    {
        $response = $this->save('Mijn game-pc', ['Ryzen 5 5600 Boxed', 'B550M Pro4']);

        $this->assertSame(201, $response->status);
        $data = $this->data($response);
        $this->assertMatchesRegularExpression('/^[a-z2-9]{8}$/', $data['share_code']);
        $this->assertSame('/build/' . $data['share_code'], $data['url']);
        $this->assertSame('Mijn game-pc', $data['name']);
    }

    public function testEveryBuildGetsItsOwnCode(): void
    {
        $first = $this->data($this->save('Eerste', ['Ryzen 5 5600 Boxed']));
        $second = $this->data($this->save('Tweede', ['Ryzen 5 5600 Boxed']));

        $this->assertNotSame($first['share_code'], $second['share_code']);
    }

    public function testSavedBuildCanBeOpenedWithCode(): void
    {
        $code = $this->data($this->save('Terug te vinden', ['Ryzen 5 5600 Boxed', 'B850M DS3H']))['share_code'];

        $response = $this->get("/api/builds/$code");

        $this->assertSame(200, $response->status);
        $data = $this->data($response);
        $this->assertSame('Terug te vinden', $data['name']);
        $this->assertSame($code, $data['share_code']);
        $this->assertEqualsCanonicalizing(['Ryzen 5 5600 Boxed', 'B850M DS3H'], array_column($data['parts'], 'name'));
        $this->assertSame('error', $data['check']['overall_status']);
        $this->assertSame(258.96, $data['check']['total_price_eur']);
    }

    public function testCodeInCapitalsAlsoWorks(): void
    {
        $code = $this->data($this->save('Hoofdletters', ['Ryzen 5 5600 Boxed']))['share_code'];

        $this->assertSame(200, $this->get('/api/builds/' . strtoupper($code))->status);
    }

    public function testUnknownCodeGivesNotFound(): void
    {
        $response = $this->get('/api/builds/onbekend');

        $this->assertError($response, 404, 'build_not_found');
        $this->assertSame('Geen build gevonden met deze code.', $this->data($response)['error']['message']);
    }

    public function testNameIsRequired(): void
    {
        foreach (['', '   '] as $name) {
            $this->assertError($this->save($name, ['Ryzen 5 5600 Boxed']), 422, 'invalid_name');
        }

        $response = $this->postJson('/api/builds', ['part_ids' => [$this->partId('Ryzen 5 5600 Boxed')]]);
        $this->assertError($response, 422, 'invalid_name');
    }

    public function testNameIsTrimmed(): void
    {
        $data = $this->data($this->save('  Spaties  ', ['Ryzen 5 5600 Boxed']));

        $this->assertSame('Spaties', $data['name']);
    }

    public function testNameOfSixtyCharactersIsAllowed(): void
    {
        $this->assertSame(201, $this->save(str_repeat('a', 60), ['Ryzen 5 5600 Boxed'])->status);
    }

    public function testNameOfSixtyOneCharactersIsNotAllowed(): void
    {
        $response = $this->save(str_repeat('a', 61), ['Ryzen 5 5600 Boxed']);

        $this->assertError($response, 422, 'invalid_name');
    }

    public function testNameLengthCountsCharactersNotBytes(): void
    {
        $this->assertSame(201, $this->save(str_repeat('é', 60), ['Ryzen 5 5600 Boxed'])->status);
    }

    public function testBuildWithoutPartsCannotBeSaved(): void
    {
        $this->assertError($this->save('Leeg', []), 422, 'empty_build');
    }

    public function testNameThatIsNotTextGivesError(): void
    {
        $response = $this->postJson('/api/builds', ['name' => 123, 'part_ids' => [1]]);

        $this->assertError($response, 422, 'invalid_name');
    }

    public function testInvalidPartsCannotBeSaved(): void
    {
        $response = $this->postJson('/api/builds', ['name' => 'Fout', 'part_ids' => [999999]]);

        $this->assertError($response, 422, 'unknown_part');
    }

    public function testHtmlInNameIsStoredAsPlainText(): void
    {
        $name = '<script>alert(1)</script>';

        $data = $this->data($this->save($name, ['Ryzen 5 5600 Boxed']));

        $this->assertSame($name, $data['name']);
    }

    private function save(string $name, array $partNames): Response
    {
        $ids = array_map(fn (string $partName) => $this->partId($partName), $partNames);

        return $this->postJson('/api/builds', ['name' => $name, 'part_ids' => $ids]);
    }
}
