<?php
$priceDifference = fn (int $cents) => match (true) {
    $cents > 0 => $this->money($cents) . ' duurder',
    $cents < 0 => $this->money(-$cents) . ' goedkoper',
    default => 'Zelfde prijs',
};
$powerDifference = fn (int $watt) => match (true) {
    $watt > 0 => "$watt W meer",
    $watt < 0 => -$watt . ' W minder',
    default => 'Zelfde verbruik',
};
?>
<h1>Builds vergelijken</h1>
<p class="intro">Per onderdeel zie je wat er anders is. Het verschil is wat <strong><?= $this->e($second->name) ?></strong> meer of minder kost dan <strong><?= $this->e($first->name) ?></strong>.</p>

<div class="card compare-card">
    <table class="compare">
        <thead>
            <tr>
                <th scope="col">Onderdeel</th>
                <?php foreach ([$first, $second] as $saved) : ?>
                    <th scope="col"><a href="/build/<?= $this->e($saved->shareCode) ?>"><?= $this->e($saved->name) ?></a></th>
                <?php endforeach; ?>
                <th scope="col">Verschil</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (\App\Model\Category::cases() as $category) : ?>
                <?php
                $parts = [$first->build->partIn($category), $second->build->partIn($category)];
                [$firstPrice, $secondPrice] = array_map(fn ($part) => $part?->priceCents ?? 0, $parts);
                ?>
                <tr>
                    <th scope="row"><?= $this->e(ucfirst($category->label())) ?></th>
                    <?php foreach ($parts as $index => $part) : ?>
                        <?php $label = $this->e([$first, $second][$index]->name); ?>
                        <?php if ($part === null) : ?>
                            <td class="is-empty" data-label="<?= $label ?>">Nog niet gekozen</td>
                        <?php else : ?>
                            <td data-label="<?= $label ?>"><?= $this->e("{$part->brand} {$part->name}") ?><br><span class="compare-price"><?= $this->money($part->priceCents) ?></span></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <td data-label="Verschil"><?= $this->e($priceDifference($secondPrice - $firstPrice)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th scope="row">Totaal</th>
                <td data-label="<?= $this->e($first->name) ?>"><?= $this->money($first->build->totalPriceCents()) ?></td>
                <td data-label="<?= $this->e($second->name) ?>"><?= $this->money($second->build->totalPriceCents()) ?></td>
                <td data-label="Verschil"><?= $this->e($priceDifference($second->build->totalPriceCents() - $first->build->totalPriceCents())) ?></td>
            </tr>
            <tr>
                <th scope="row">Verbruik</th>
                <td data-label="<?= $this->e($first->name) ?>">ca. <?= $first->build->estimatedPowerW() ?> W</td>
                <td data-label="<?= $this->e($second->name) ?>">ca. <?= $second->build->estimatedPowerW() ?> W</td>
                <td data-label="Verschil"><?= $this->e($powerDifference($second->build->estimatedPowerW() - $first->build->estimatedPowerW())) ?></td>
            </tr>
        </tfoot>
    </table>
</div>
