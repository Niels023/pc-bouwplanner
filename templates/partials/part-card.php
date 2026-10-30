<?php
$categoryValue = $part->category()->value;
$fullName = "{$part->brand} {$part->name}";
$classes = ['part-card'];
if ($chosen) {
    $classes[] = 'is-chosen';
}
if ($conflict) {
    $classes[] = 'is-conflict';
} elseif ($problems !== []) {
    $classes[] = 'is-warning';
}
?>
<article class="<?= implode(' ', $classes) ?>" data-part="<?= $part->id ?>">
    <h3><?= $this->e($fullName) ?></h3>
    <ul class="specs">
        <?php foreach ($part->description() as $line) : ?>
            <li><?= $this->e($line) ?></li>
        <?php endforeach; ?>
        <?php if ($part->powerW > 0) : ?>
            <li>Verbruik <?= $part->powerW ?> W</li>
        <?php endif; ?>
    </ul>
    <p class="price">
        <strong><?= $this->money($part->priceCents) ?></strong>
        <small>peildatum <?= $this->date($part->priceDate) ?></small>
    </p>
    <?php foreach ($problems as $problem) : ?>
        <p class="note note-<?= $this->e($problem->status->value) ?>">
            <strong><?= $this->e($problem->status->label()) ?>:</strong> <?= $this->e($problem->message) ?>
        </p>
    <?php endforeach; ?>
    <?php if ($chosen) : ?>
        <p class="chosen-label">Gekozen</p>
        <form action="/bouwen/<?= $this->e($categoryValue) ?>/verwijderen<?= $this->e($queryString) ?>" method="post" data-live data-part="<?= $part->id ?>">
            <button type="submit" class="button button-secondary">Verwijderen<span class="visually-hidden"> <?= $this->e($fullName) ?></span></button>
        </form>
    <?php else : ?>
        <form action="/bouwen/<?= $this->e($categoryValue) ?>/kiezen<?= $this->e($queryString) ?>" method="post" data-live data-part="<?= $part->id ?>">
            <input type="hidden" name="part_id" value="<?= $part->id ?>">
            <button type="submit" class="button">Kies<span class="visually-hidden"> <?= $this->e($fullName) ?></span></button>
        </form>
    <?php endif; ?>
</article>
