<ul class="build-list">
    <?php foreach (\App\Model\Category::cases() as $category) : ?>
        <?php $part = $build->partIn($category); ?>
        <?php if ($part !== null || $editable) : ?>
            <li>
                <span class="build-list-category"><?= $this->e(ucfirst($category->label())) ?></span>
                <span class="build-list-part"><?= $part === null ? 'Nog niet gekozen' : $this->e("{$part->brand} {$part->name}") ?></span>
                <span class="build-list-price"><?= $part === null ? '' : $this->money($part->priceCents) ?></span>
                <?php if ($editable) : ?>
                    <a href="/bouwen/<?= $this->e($category->value) ?>"><?= $part === null ? 'Kiezen' : 'Wijzigen' ?><span class="visually-hidden"> <?= $this->e($category->label()) ?></span></a>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
