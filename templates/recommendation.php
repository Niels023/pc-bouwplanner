<h1>Laat een build voorstellen</h1>
<p class="intro">Vul je budget en doel in. Je krijgt een complete build die past, met bij elk onderdeel de reden waarom het gekozen is.</p>

<form action="/aanbeveling" method="get" class="recommendation-form">
    <?php if ($error !== null) : ?>
        <p class="message message-error" role="alert"><?= $this->e($error) ?></p>
    <?php endif; ?>
    <div class="field">
        <label for="budget">Budget in hele euro's</label>
        <input id="budget" name="budget" type="number" min="1" step="1" value="<?= $this->e($budget) ?>">
    </div>
    <fieldset>
        <legend>Waar ga je de PC vooral voor gebruiken?</legend>
        <?php foreach ($goals as $goal) : ?>
            <label class="radio">
                <input type="radio" name="goal" value="<?= $this->e($goal->value) ?>"
                    <?= $selectedGoal === $goal->value ? 'checked' : '' ?>>
                <?= $this->e(ucfirst($goal->label())) ?>
            </label>
        <?php endforeach; ?>
    </fieldset>
    <button type="submit" class="button">Build voorstellen</button>
</form>

<?php if ($recommendation !== null) : ?>
    <section aria-labelledby="result-title">
        <h2 id="result-title">Jouw build</h2>
        <?= $this->partial('partials/totals', ['report' => $report]) ?>
        <p>Over van je budget: <strong><?= $this->money($recommendation->leftoverCents()) ?></strong></p>
        <ul class="recommendation-list">
            <?php foreach ($recommendation->choices as $choice) : ?>
                <?php $part = $choice->part; ?>
                <li>
                    <span class="build-list-category"><?= $this->e(ucfirst($part->category()->label())) ?></span>
                    <span class="build-list-part"><?= $this->e("{$part->brand} {$part->name}") ?></span>
                    <span class="build-list-price"><?= $this->money($part->priceCents) ?></span>
                    <span class="recommendation-reason"><?= $this->e($choice->reason) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <?= $this->partial('partials/check-results', ['report' => $report]) ?>
        <form action="/aanbeveling/overnemen?<?= $this->e($query) ?>" method="post">
            <button type="submit" class="button">Overnemen en zelf aanpassen</button>
        </form>
    </section>
<?php endif; ?>
