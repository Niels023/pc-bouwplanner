<h1>Overzicht van je build</h1>

<?= $this->partial('partials/build-list', ['build' => $build, 'editable' => true]) ?>
<?= $this->partial('partials/totals', ['report' => $report]) ?>
<?= $this->partial('partials/check-results', ['report' => $report]) ?>

<section class="save-build" aria-labelledby="save-title">
    <h2 id="save-title">Build opslaan</h2>
    <?php if ($error !== null) : ?>
        <p class="message message-error" role="alert"><?= $this->e($error) ?></p>
    <?php endif; ?>
    <form action="/opslaan" method="post" class="inline-form">
        <div class="field">
            <label for="name">Naam van je build</label>
            <input id="name" name="name" type="text" value="<?= $this->e($name) ?>" required>
        </div>
        <button type="submit" class="button">Opslaan</button>
    </form>
</section>
