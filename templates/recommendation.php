<h1>Laat een build voorstellen</h1>
<p class="intro">Vertel wat je met je PC wilt doen. Je krijgt een complete build die past, met bij elk onderdeel de reden waarom het gekozen is.</p>

<form action="/aanbeveling" method="get" class="recommendation-form">
    <?php if ($error !== null) : ?>
        <div class="message message-error" role="alert">
            <p><?= $this->e($error) ?></p>
            <?php if ($cheapestUrl !== null) : ?>
                <p><a href="<?= $this->e($cheapestUrl) ?>">Bekijk de goedkoopste build voor deze keuzes</a></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

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

    <div class="game-options">
        <fieldset>
            <legend>Op wat voor scherm speel je?</legend>
            <?php foreach ($resolutions as $resolution) : ?>
                <label class="radio">
                    <input type="radio" name="resolution" value="<?= $this->e($resolution->value) ?>"
                        <?= $selectedResolution === $resolution->value ? 'checked' : '' ?>>
                    <?= $this->e($resolution->description()) ?>
                </label>
            <?php endforeach; ?>
        </fieldset>
        <fieldset>
            <legend>Wat voor games speel je?</legend>
            <?php foreach ($gameTypes as $gameType) : ?>
                <label class="radio">
                    <input type="radio" name="game_type" value="<?= $this->e($gameType->value) ?>"
                        <?= $selectedGameType === $gameType->value ? 'checked' : '' ?>>
                    <?= $this->e($gameType->description()) ?>
                </label>
            <?php endforeach; ?>
        </fieldset>
    </div>

    <div class="field">
        <label for="budget">Budget in hele euro's</label>
        <input id="budget" name="budget" type="number" min="1" step="1" value="<?= $this->e($budget) ?>" aria-describedby="budget-hint">
        <p class="hint" id="budget-hint">Weet je het niet? Laat dit leeg, dan krijg je de goedkoopste build die bij je keuzes past.</p>
    </div>

    <button type="submit" class="button">Build voorstellen</button>
</form>

<?php if ($recommendation !== null) : ?>
    <section aria-labelledby="result-title">
        <h2 id="result-title">Jouw build</h2>
        <?php if ($recommendation->summary() !== null) : ?>
            <p class="summary"><?= $this->e($recommendation->summary()) ?></p>
        <?php endif; ?>
        <?= $this->partial('partials/totals', ['report' => $report]) ?>
        <?php if ($recommendation->budgetGiven) : ?>
            <p>Over van je budget: <strong><?= $this->money($recommendation->leftoverCents()) ?></strong></p>
        <?php else : ?>
            <p>Dit is de goedkoopste build die bij je keuzes past.</p>
        <?php endif; ?>
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
