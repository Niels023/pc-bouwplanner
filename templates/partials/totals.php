<?php $status = $report->overallStatus(); ?>
<section class="totals" id="totals" aria-live="polite" aria-label="Totaal van je build">
    <p>Totaal <strong><?= $this->money($report->totalPriceCents) ?></strong></p>
    <p>Verbruik <strong>ca. <?= $report->estimatedPowerW ?> W</strong></p>
    <p>Status <strong class="status status-<?= $this->e($status->value) ?>"><?= $this->e($status->label()) ?></strong></p>
</section>
