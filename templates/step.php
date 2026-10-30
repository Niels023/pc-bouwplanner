<?php
$number = $category->stepNumber();
$total = count($category::cases());
$query = http_build_query($filters);
$queryString = $query === '' ? '' : "?$query";
$selects = [
    'brand' => ['Merk', 'Alle merken'],
    'socket' => ['Socket', 'Alle sockets'],
    'ram_type' => ['RAM-type', 'Alle types'],
    'form_factor' => ['Formaat', 'Alle formaten'],
];
?>
<h1>Stap <?= $number ?> van <?= $total ?>: <?= $this->e(ucfirst($category->label())) ?></h1>

<div id="steps">
    <?= $this->partial('partials/steps', ['current' => $category, 'build' => $build]) ?>
</div>

<div class="step-layout">
    <details class="filters" id="filters" open<?= $filters === [] ? '' : ' data-keep-open' ?>>
        <summary>Filters</summary>
        <form action="/bouwen/<?= $this->e($category->value) ?>" method="get">
            <?php foreach ($selects as $name => [$label, $allLabel]) : ?>
                <?php if (count($options[$name]) > 1) : ?>
                    <div class="field">
                        <label for="<?= $name ?>"><?= $label ?></label>
                        <select id="<?= $name ?>" name="<?= $name ?>">
                            <option value=""><?= $allLabel ?></option>
                            <?php foreach ($options[$name] as $option) : ?>
                                <option value="<?= $this->e($option) ?>"<?= ($filters[$name] ?? '') === $option ? ' selected' : '' ?>><?= $this->e($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="field">
                <label for="max_price">Maximale prijs in euro</label>
                <input id="max_price" name="max_price" type="number" min="1" step="1" value="<?= $this->e($filters['max_price'] ?? '') ?>">
            </div>
            <button type="submit" class="button">Filters toepassen</button>
            <?php if ($filters !== []) : ?>
                <a class="clear-filters" href="/bouwen/<?= $this->e($category->value) ?>">Filters wissen</a>
            <?php endif; ?>
        </form>
    </details>

    <section aria-labelledby="parts-title">
        <h2 id="parts-title" class="visually-hidden">Onderdelen</h2>
        <?php if ($filterError !== null) : ?>
            <p class="message message-error" role="alert"><?= $this->e($filterError) ?></p>
        <?php endif; ?>
        <div id="parts">
            <?php if ($cards === []) : ?>
                <p class="message message-info">Geen onderdelen gevonden met deze filters.</p>
            <?php else : ?>
                <div class="cards">
                    <?php foreach ($cards as $card) : ?>
                        <?= $this->partial('partials/part-card', $card + ['queryString' => $queryString]) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= $this->partial('partials/totals', ['report' => $report]) ?>

<nav class="step-links" aria-label="Volgende en vorige stap">
    <?php if ($category->previous() !== null) : ?>
        <a class="button button-secondary" href="/bouwen/<?= $this->e($category->previous()->value) ?>">Vorige stap</a>
    <?php else : ?>
        <span></span>
    <?php endif; ?>
    <?php if ($category->next() !== null) : ?>
        <a class="button" href="/bouwen/<?= $this->e($category->next()->value) ?>">Volgende stap</a>
    <?php else : ?>
        <a class="button" href="/overzicht">Naar het overzicht</a>
    <?php endif; ?>
</nav>
