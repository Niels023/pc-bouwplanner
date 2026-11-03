<ul class="build-list">
    <?php foreach (\App\Model\Category::cases() as $category) : ?>
        <?php $part = $build->partIn($category); ?>
        <?php if ($part !== null || $editable) : ?>
            <li>
                <span class="build-list-category"><?= $this->e(ucfirst($category->label())) ?></span>
                <?php if ($part === null) : ?>
                    <span class="build-list-part is-empty">Nog niet gekozen</span>
                <?php else : ?>
                    <span class="build-list-part"><?= $this->e("{$part->brand} {$part->name}") ?></span>
                    <span class="build-list-price"><?= $this->money($part->priceCents) ?></span>
                <?php endif; ?>
                <?php if ($editable) : ?>
                    <a href="/bouwen/<?= $this->e($category->value) ?>"><?= $part === null ? 'Kiezen' : 'Wijzigen' ?><span class="visually-hidden"> <?= $this->e($category->label()) ?></span></a>
                <?php endif; ?>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
