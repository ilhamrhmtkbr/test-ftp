<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
?>

<div class="form-group">
    <label for="<?= $name ?>" class="font-weight-bold"><?= StringHelper::toCapitalize($name) ?></label>

    <select
            name="<?= $name ?>"
            id="<?= $name ?>"
            class="form-control <?= isset($session['errors'][$name]) ? 'is-invalid' : '' ?>"
    >
        <?php if (array_keys($options) !== range(0, count($options) - 1)) : ?>
            <?php foreach ($options as $value => $label) : ?>
                <option
                        value="<?= $value ?>"
                        <?= (isset($selectedValue) && $selectedValue == $value) ? 'selected' : '' ?>
                        style="text-transform: capitalize;"
                >
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        <?php else : ?>
            <?php foreach ($options as $item) : ?>
                <option
                        value="<?= $item ?>"
                        <?= (isset($selectedValue) && $selectedValue == $item) ? 'selected' : '' ?>
                        style="text-transform: capitalize;"
                >
                    <?= $item ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>

    <?php if (isset($additionalInfo)) : ?>
        <small class="form-text text-muted"><?= $additionalInfo ?></small>
    <?php endif; ?>

    <?php if (isset($session['errors'][$name])) : ?>
        <div class="invalid-feedback d-block">
            <?= $session['errors'][$name][0] ?>
        </div>
    <?php endif; ?>
</div>
