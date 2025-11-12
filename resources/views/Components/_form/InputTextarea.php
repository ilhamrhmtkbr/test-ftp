<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
?>

<div class="form-group">
    <label for="<?= $name ?>" class="font-weight-bold">
        <?= StringHelper::toCapitalize($name) ?>
    </label>

    <textarea
            name="<?= $name ?>"
            id="<?= $name ?>"
            rows="4"
            class="form-control <?= isset($session['errors'][$name]) ? 'is-invalid' : '' ?>"
            placeholder="Write something here..."
            style="resize: none;"
    ><?= isset($value[$name]) ? htmlspecialchars($value[$name]) : '' ?></textarea>

    <?php if (isset($session['errors'][$name])) : ?>
        <div class="invalid-feedback d-block">
            <?= $session['errors'][$name][0] ?>
        </div>
    <?php endif; ?>
</div>
