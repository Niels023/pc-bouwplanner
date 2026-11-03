<?php $status = $report->overallStatus(); ?>
<section class="totals" id="totals" aria-live="polite" aria-label="Totaal van je build">
    <p><span class="totals-label">Totaal</span> <strong><?= $this->money($report->totalPriceCents) ?></strong></p>
    <p><span class="totals-label">Verbruik</span> <strong>ca. <?= $report->estimatedPowerW ?> W</strong></p>
    <p><span class="totals-label">Status</span> <strong class="status status-<?= $this->e($status->value) ?>"><?= $this->e($status->label()) ?></strong></p>
</section>
