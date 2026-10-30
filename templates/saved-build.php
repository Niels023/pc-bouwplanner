<h1><?= $this->e($saved->name) ?></h1>

<p>
    Deelcode: <strong><?= $this->e($saved->shareCode) ?></strong>.
    Met deze code of <a href="/build/<?= $this->e($saved->shareCode) ?>">deze link</a> open je de build later weer.
</p>

<?= $this->partial('partials/build-list', ['build' => $saved->build, 'editable' => false]) ?>
<?= $this->partial('partials/totals', ['report' => $report]) ?>
<?= $this->partial('partials/check-results', ['report' => $report]) ?>
