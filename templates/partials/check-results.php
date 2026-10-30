<h2>Controle</h2>
<ul class="check-results">
    <?php foreach ($report->results as $result) : ?>
        <li class="check-result check-<?= $this->e($result->status->value) ?>">
            <strong><?= $this->e($result->status->label()) ?></strong>
            <span><?= $this->e($result->message) ?></span>
        </li>
    <?php endforeach; ?>
</ul>
