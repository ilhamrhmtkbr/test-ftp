<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
?>

<div class="form-group">
    <label for="<?= $name ?>" class="font-weight-bold">
        <?= StringHelper::toCapitalize($name) ?>
    </label>

    <input
            type="<?= $inputType ?? 'text' ?>"
            name="<?= $name ?>"
            id="<?= $name ?>"
            class="form-control <?= isset($session['errors'][$name]) ? 'is-invalid' : '' ?>"
            value="<?= isset($value[$name]) ? htmlspecialchars($value[$name]) : '' ?>"
    >

    <?php if (isset($session['errors'][$name])) : ?>
        <div class="invalid-feedback d-block">
            <?= $session['errors'][$name][0] ?>
        </div>
    <?php endif; ?>
</div>
