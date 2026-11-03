<?php

namespace App\Compatibility;

use App\Model\Build;
use App\Model\Category;
use App\Model\Part;

class CompatibilityChecker
{
    // Per regel de onderdelen die nodig zijn. Ontbreekt er een, dan wordt de regel nog niet gecontroleerd.
    private const RULES = [
        'socket' => [Category::Cpu, Category::Motherboard],
        'ram_type' => [Category::Motherboard, Category::Ram],
        'form_factor' => [Category::Motherboard, Category::PcCase],
        // Zonder processor valt het verbruik zo laag uit dat elke voeding goed lijkt.
        'psu_wattage' => [Category::Psu, Category::Cpu],
        'gpu_length' => [Category::PcCase],
        'cooler_height' => [Category::Cooler, Category::PcCase],
        'radiator' => [Category::Cooler, Category::PcCase],
        'cooler_socket' => [Category::Cooler, Category::Cpu],
        'graphics' => [Category::Cpu],
    ];

    public function __construct(private int $psuMarginPercent)
    {
    }

    public function check(Build $build): CheckReport
    {
        $results = [];
        foreach (array_keys(self::RULES) as $rule) {
            $results[] = $this->runRule($rule, $build);
        }

        return new CheckReport($results, $build->isComplete(), $build->totalPriceCents(), $build->estimatedPowerW());
    }

    /**
     * De fouten en waarschuwingen die dit onderdeel zou geven als je het kiest.
     *
     * @return RuleResult[]
     */
    public function problemsFor(Build $build, Part $candidate): array
    {
        $withCandidate = $build->withPart($candidate);
        $problems = [];

        foreach (self::RULES as $rule => $categories) {
            $result = $this->runRule($rule, $withCandidate);
            if (!$result->isProblem()) {
                continue;
            }

            // Een probleem tussen andere onderdelen ligt niet aan dit onderdeel.
            $usesCandidate = in_array($candidate->category(), $categories, true);
            if ($usesCandidate || !$this->runRule($rule, $build)->isProblem()) {
                $problems[] = $result;
            }
        }

        return $problems;
    }

    private function runRule(string $rule, Build $build): RuleResult
    {
        $missing = array_filter(self::RULES[$rule], fn (Category $category) => !$build->hasPart($category));

        if ($missing !== []) {
            $names = array_map(fn (Category $category) => $category->withArticle(), $missing);

            return new RuleResult($rule, CheckStatus::NotChecked, 'Kies eerst ' . implode(' en ', $names) . '.');
        }

        // Elke regel geeft een status en een melding terug.
        [$status, $message] = match ($rule) {
            'socket' => $this->socketRule($build),
            'ram_type' => $this->ramTypeRule($build),
            'form_factor' => $this->formFactorRule($build),
            'psu_wattage' => $this->psuWattageRule($build),
            'gpu_length' => $this->gpuLengthRule($build),
            'cooler_height' => $this->coolerHeightRule($build),
            'radiator' => $this->radiatorRule($build),
            'cooler_socket' => $this->coolerSocketRule($build),
            'graphics' => $this->graphicsRule($build),
        };

        return new RuleResult($rule, $status, $message);
    }

    private function socketRule(Build $build): array
    {
        $cpuSocket = $build->cpu()->socket;
        $boardSocket = $build->motherboard()->socket;

        if ($cpuSocket !== $boardSocket) {
            $message = "De processor heeft socket $cpuSocket, maar het moederbord heeft socket $boardSocket.";

            return [CheckStatus::Error, $message];
        }

        $message = "De processor en het moederbord hebben allebei socket $cpuSocket.";

        return [CheckStatus::Ok, $message];
    }

    private function ramTypeRule(Build $build): array
    {
        $ramType = $build->ram()->ramType;
        $boardRamType = $build->motherboard()->ramType;

        if ($ramType !== $boardRamType) {
            $message = "Het geheugen is $ramType, maar het moederbord ondersteunt alleen $boardRamType.";

            return [CheckStatus::Error, $message];
        }

        $message = "Het geheugen en het moederbord gebruiken allebei $ramType.";

        return [CheckStatus::Ok, $message];
    }

    private function formFactorRule(Build $build): array
    {
        $board = $build->motherboard()->formFactor;
        $case = $build->pcCase()->maxFormFactor;

        if (!$board->fitsIn($case)) {
            $message = "Het moederbord is {$board->name}, maar in deze kast past maximaal {$case->name}.";

            return [CheckStatus::Error, $message];
        }

        $message = "Het moederbord ({$board->name}) past in deze kast (maximaal {$case->name}).";

        return [CheckStatus::Ok, $message];
    }

    private function psuWattageRule(Build $build): array
    {
        $wattage = $build->psu()->wattageW;
        $usage = $build->estimatedPowerW();
        $margin = $this->psuMarginPercent;
        $needed = (int) ceil($usage * (100 + $margin) / 100);

        if ($wattage < $usage) {
            $message = "De voeding levert $wattage W, maar de onderdelen verbruiken samen ongeveer $usage W.";

            return [CheckStatus::Error, $message];
        }

        if ($wattage < $needed) {
            $message = "De voeding levert $wattage W. Dat is genoeg voor de ongeveer $usage W die de "
                . "onderdelen verbruiken, maar met $margin% marge is $needed W beter.";

            return [CheckStatus::Warning, $message];
        }

        $message = "De voeding levert $wattage W. De onderdelen verbruiken ongeveer $usage W, "
            . "met $margin% marge is $needed W nodig.";

        return [CheckStatus::Ok, $message];
    }

    private function gpuLengthRule(Build $build): array
    {
        if (!$build->hasPart(Category::Gpu)) {
            $message = 'Er is geen videokaart gekozen die moet passen.';

            return [CheckStatus::Ok, $message];
        }

        $length = $build->gpu()->lengthMm;
        $maxLength = $build->pcCase()->maxGpuLengthMm;

        if ($length > $maxLength) {
            $message = "De videokaart is $length mm lang, maar in deze kast past maximaal $maxLength mm.";

            return [CheckStatus::Error, $message];
        }

        $message = "De videokaart ($length mm) past in deze kast (maximaal $maxLength mm).";

        return [CheckStatus::Ok, $message];
    }

    private function coolerHeightRule(Build $build): array
    {
        if ($build->cooler()->isLiquidCooler()) {
            $message = 'Een waterkoeler heeft geen hoge koeltoren, dus de hoogte telt niet.';

            return [CheckStatus::Ok, $message];
        }

        $height = $build->cooler()->heightMm;
        $maxHeight = $build->pcCase()->maxCoolerHeightMm;

        if ($height > $maxHeight) {
            $message = "De koeler is $height mm hoog, maar in deze kast past maximaal $maxHeight mm.";

            return [CheckStatus::Error, $message];
        }

        $message = "De koeler ($height mm) past in deze kast (maximaal $maxHeight mm).";

        return [CheckStatus::Ok, $message];
    }

    private function radiatorRule(Build $build): array
    {
        if (!$build->cooler()->isLiquidCooler()) {
            $message = 'Er is geen waterkoeler gekozen, dus er hoeft geen radiator in de kast.';

            return [CheckStatus::Ok, $message];
        }

        $radiator = $build->cooler()->radiatorMm;
        $maxRadiator = $build->pcCase()->maxRadiatorMm;

        if ($radiator > $maxRadiator) {
            $message = "De radiator is $radiator mm, maar in deze kast past maximaal $maxRadiator mm.";

            return [CheckStatus::Error, $message];
        }

        $message = "De radiator ($radiator mm) past in deze kast (maximaal $maxRadiator mm).";

        return [CheckStatus::Ok, $message];
    }

    private function coolerSocketRule(Build $build): array
    {
        $socket = $build->cpu()->socket;
        $coolerSockets = $build->cooler()->sockets;

        if (!in_array($socket, $coolerSockets, true)) {
            $message = "De koeler past niet op socket $socket. Hij past op " . implode(', ', $coolerSockets) . '.';

            return [CheckStatus::Error, $message];
        }

        $message = "De koeler past op socket $socket.";

        return [CheckStatus::Ok, $message];
    }

    private function graphicsRule(Build $build): array
    {
        if ($build->hasPart(Category::Gpu)) {
            $message = 'Er is een videokaart gekozen, dus de PC geeft beeld.';

            return [CheckStatus::Ok, $message];
        }

        if ($build->cpu()->hasIntegratedGraphics) {
            $message = 'De processor heeft ingebouwde graphics, dus een videokaart is niet nodig.';

            return [CheckStatus::Ok, $message];
        }

        // Pas een fout als de rest gekozen is: daarvoor kan de videokaart nog komen.
        if ($build->hasAllExceptGpu()) {
            $message = 'Deze processor heeft geen ingebouwde graphics. Zonder videokaart krijg je geen beeld.';

            return [CheckStatus::Error, $message];
        }

        $message = 'Kies nog een videokaart, want deze processor heeft geen ingebouwde graphics.';

        return [CheckStatus::NotChecked, $message];
    }
}
