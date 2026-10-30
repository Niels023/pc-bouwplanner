<?php

namespace Tests\Pages;

use App\Http\Response;
use App\Model\Category;

class BuildStepPageTest extends PageTestCase
{
    public function testStepShowsPartsAsCards(): void
    {
        $response = $this->get('/bouwen/cpu');

        $this->assertSame(200, $response->status);
        $this->assertPageContains('Stap 1 van 8', $response);
        $this->assertPageContains('Ryzen 5 7600 Boxed', $response);
        $this->assertPageContains('€ 176,82', $response);
        $this->assertPageContains('28-10-2026', $response);
        $this->assertPageContains('Socket AM5', $response);
    }

    public function testAllEightStepsExistInTheRightOrder(): void
    {
        foreach (Category::cases() as $index => $category) {
            $number = $index + 1;

            $response = $this->get("/bouwen/{$category->value}");

            $this->assertSame(200, $response->status);
            $this->assertPageContains("Stap $number van 8", $response);
        }
    }

    public function testUnknownStepGivesNotFound(): void
    {
        $this->assertSame(404, $this->get('/bouwen/toetsenbord')->status);
    }

    public function testEveryCategoryShowsItsOwnSpecs(): void
    {
        $expected = [
            'motherboard' => '4 geheugensloten',
            'ram' => 'DDR4',
            'gpu' => 'Lengte 197 mm',
            'cooler' => 'Hoogte 70 mm',
            'case' => 'Koeler tot 70 mm',
            'storage' => 'NVMe',
            'psu' => '550 W',
        ];

        foreach ($expected as $category => $text) {
            $this->assertPageContains($text, $this->get("/bouwen/$category"));
        }
    }

    public function testFilterShowsOnlyMatchingParts(): void
    {
        $response = $this->get('/bouwen/cpu', ['brand' => 'Intel']);

        $this->assertPageContains('Core i5-14600K Boxed', $response);
        $this->assertPageNotContains('Ryzen 5 7600 Boxed', $response);
    }

    public function testFiltersCanBeCombined(): void
    {
        $response = $this->get('/bouwen/cpu', ['brand' => 'AMD', 'socket' => 'AM4', 'max_price' => '150']);

        $this->assertPageContains('Ryzen 5 5600 Boxed', $response);
        $this->assertPageNotContains('Ryzen 7 5700X Boxed', $response);
        $this->assertPageNotContains('Ryzen 5 7600 Boxed', $response);
    }

    public function testFiltersStayFilledIn(): void
    {
        $response = $this->get('/bouwen/cpu', ['brand' => 'AMD', 'max_price' => '150']);

        $this->assertStringContainsString('<option value="AMD" selected>', $response->body);
        $this->assertStringContainsString('value="150"', $response->body);
    }

    public function testSortChangesTheOrderOfTheCards(): void
    {
        $body = $this->get('/bouwen/gpu', ['sort' => 'price_desc'])->body;

        $this->assertLessThan(strpos($body, 'RTX 5060 8G'), strpos($body, 'ROG Astral'));
        $this->assertStringContainsString('<option value="price_desc" selected>', $body);
    }

    public function testChoosingKeepsTheSorting(): void
    {
        $id = $this->partId('Ryzen 5 7600 Boxed');

        $response = $this->postForm('/bouwen/cpu/kiezen', ['part_id' => $id], ['sort' => 'name']);

        $this->assertRedirectsTo('/bouwen/cpu?sort=name', $response);
    }

    public function testNoResultsGivesMessage(): void
    {
        $response = $this->get('/bouwen/cpu', ['max_price' => '1']);

        $this->assertPageContains('Geen onderdelen gevonden met deze filters', $response);
    }

    public function testInvalidMaxPriceGivesMessageAndStillShowsParts(): void
    {
        $response = $this->get('/bouwen/cpu', ['max_price' => 'abc']);

        $this->assertPageContains('De maximale prijs moet een getal boven 0 zijn.', $response);
        $this->assertPageContains('Ryzen 5 7600 Boxed', $response);
    }

    public function testOnlyFiltersThatFitTheCategoryAreShown(): void
    {
        $cpu = $this->get('/bouwen/cpu');
        $cooler = $this->get('/bouwen/cooler');

        $this->assertPageContains('name="socket"', $cpu);
        $this->assertPageNotContains('name="form_factor"', $cpu);
        $this->assertPageNotContains('name="socket"', $cooler);
    }

    public function testChoosingAPartRedirectsAndShowsIt(): void
    {
        $response = $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $this->assertRedirectsTo('/bouwen/cpu', $response);
        $this->assertPageContains('Gekozen', $this->get('/bouwen/cpu'));
    }

    public function testRedirectKeepsFilters(): void
    {
        $id = $this->partId('Ryzen 5 7600 Boxed');

        $response = $this->postForm('/bouwen/cpu/kiezen', ['part_id' => $id], ['brand' => 'AMD', 'onzin' => 'x']);

        $this->assertRedirectsTo('/bouwen/cpu?brand=AMD', $response);
    }

    public function testChoosingAnotherPartReplacesTheFirst(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');
        $this->choose('cpu', 'Ryzen 5 5600 Boxed');

        $this->assertPageContains('€ 124,96', $this->totalsOf($this->get('/bouwen/cpu')));
    }

    public function testChoiceIsKeptOnOtherSteps(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $this->assertPageContains('€ 176,82', $this->totalsOf($this->get('/bouwen/psu')));
    }

    public function testTotalsAreAddedUp(): void
    {
        $this->choose('cpu', 'Ryzen 5 5600 Boxed');
        $this->choose('motherboard', 'B550M Pro4');

        $totals = $this->totalsOf($this->get('/bouwen/ram'));

        $this->assertPageContains('€ 220,44', $totals);
        $this->assertPageContains('128 W', $totals);
    }

    public function testRemovingAPartEmptiesItsStep(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $response = $this->postForm('/bouwen/cpu/verwijderen');

        $this->assertRedirectsTo('/bouwen/cpu', $response);
        $this->assertPageContains('€ 0,00', $this->totalsOf($this->get('/bouwen/cpu')));
    }

    public function testUnknownPartCannotBeChosen(): void
    {
        $this->assertSame(404, $this->postForm('/bouwen/cpu/kiezen', ['part_id' => 999999])->status);
        $this->assertSame(404, $this->postForm('/bouwen/cpu/kiezen')->status);
    }

    public function testPartOfOtherCategoryCannotBeChosen(): void
    {
        $id = $this->partId('B550M Pro4');

        $this->assertSame(404, $this->postForm('/bouwen/cpu/kiezen', ['part_id' => $id])->status);
    }

    public function testPartsThatDoNotFitAreGreyWithReason(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $response = $this->get('/bouwen/motherboard');

        $this->assertStringContainsString('is-conflict', $response->body);
        $this->assertPageContains('De processor heeft socket AM5, maar het moederbord heeft socket AM4.', $response);
    }

    public function testPartsThatDoNotFitCanStillBeChosen(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $this->assertRedirectsTo('/bouwen/motherboard', $this->choose('motherboard', 'B550M Pro4'));
        $this->assertPageContains('Fout', $this->totalsOf($this->get('/bouwen/motherboard')));
    }

    public function testPartsThatFitComeFirst(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $body = $this->get('/bouwen/motherboard')->body;

        $this->assertLessThan(strpos($body, 'B550M Pro4'), strpos($body, 'B850M DS3H'));
    }

    public function testNothingIsGreyWithoutChoices(): void
    {
        $this->assertStringNotContainsString('is-conflict', $this->get('/bouwen/motherboard')->body);
    }

    public function testTightPsuGivesWarningOnCard(): void
    {
        $this->choose('cpu', 'Ryzen 7 9800X3D Boxed');
        $this->choose('gpu', 'ROG Astral GeForce RTX 5080 16GB GDDR7 OC Edition');

        $response = $this->get('/bouwen/psu');

        $this->assertStringContainsString('is-warning', $response->body);
        $this->assertPageContains('De voeding levert 650 W', $response);
    }

    public function testStepNavigationLinksToAllSteps(): void
    {
        $response = $this->get('/bouwen/ram');

        foreach (Category::cases() as $category) {
            $this->assertStringContainsString("href=\"/bouwen/{$category->value}\"", $response->body);
        }
        $this->assertStringContainsString('aria-current="step"', $response->body);
    }

    public function testFirstAndLastStepHaveTheRightLinks(): void
    {
        $this->assertPageNotContains('Vorige stap', $this->get('/bouwen/cpu'));
        $this->assertPageContains('href="/bouwen/motherboard"', $this->get('/bouwen/cpu'));
        $this->assertPageContains('href="/overzicht"', $this->get('/bouwen/psu'));
    }

    public function testGpuStepSaysWhenAGpuIsOptional(): void
    {
        $this->choose('cpu', 'Ryzen 5 7600 Boxed');

        $this->assertPageContains('is een videokaart niet nodig', $this->get('/bouwen/gpu'));
    }

    public function testGpuStepDoesNotSayThatWithoutIntegratedGraphics(): void
    {
        $this->choose('cpu', 'Ryzen 5 5600 Boxed');

        $this->assertPageNotContains('is een videokaart niet nodig', $this->get('/bouwen/gpu'));
    }

    public function testHtmlInFilterIsEscaped(): void
    {
        $response = $this->get('/bouwen/cpu', ['max_price' => '"><script>alert(1)</script>']);

        $this->assertStringNotContainsString('<script>alert(1)', $response->body);
    }

    private function choose(string $category, string $partName): Response
    {
        return $this->postForm("/bouwen/$category/kiezen", ['part_id' => $this->partId($partName)]);
    }

    // Alleen het blok met de totalen, zodat prijzen op de kaartjes de test niet laten slagen.
    private function totalsOf(Response $response): Response
    {
        preg_match('#<section class="totals".*?</section>#s', $response->body, $matches);

        return Response::html($matches[0]);
    }
}
