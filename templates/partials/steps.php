<ol class="steps">
    <?php foreach ($current::cases() as $step) : ?>
        <?php $isCurrent = $step === $current; ?>
        <li class="<?= $build->hasPart($step) ? 'is-done' : '' ?>">
            <a href="/bouwen/<?= $this->e($step->value) ?>"<?= $isCurrent ? ' aria-current="step"' : '' ?>>
                <span class="step-number"><?= $step->stepNumber() ?></span>
                <span class="step-label"><?= $this->e(ucfirst($step->label())) ?></span>
                <?php if ($build->hasPart($step)) : ?>
                    <span class="visually-hidden">(gekozen)</span>
                <?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>
</ol>
