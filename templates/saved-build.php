<h1><?= $this->e($saved->name) ?></h1>

<p class="intro">
    Deelcode <span class="share-code"><?= $this->e($saved->shareCode) ?></span><br>
    Met deze code of <a href="/build/<?= $this->e($saved->shareCode) ?>">deze link</a> open je de build later weer.
</p>
<p class="hint print-hint">Afdrukken kan met Ctrl+P. Het menu en de knoppen komen dan niet op papier.</p>

<?= $this->partial('partials/totals', ['report' => $report]) ?>
<?= $this->partial('partials/build-list', ['build' => $saved->build, 'editable' => false]) ?>
<?= $this->partial('partials/check-results', ['report' => $report]) ?>

<section class="card compare-form" aria-labelledby="compare-title">
    <h2 id="compare-title">Vergelijken met een andere build</h2>
    <form action="/vergelijken" method="get" class="inline-form">
        <input type="hidden" name="a" value="<?= $this->e($saved->shareCode) ?>">
        <div class="field">
            <label for="b">Deelcode van de andere build</label>
            <input id="b" name="b" type="text" maxlength="8" autocomplete="off" required>
        </div>
        <button type="submit" class="button">Vergelijken</button>
    </form>
</section>
