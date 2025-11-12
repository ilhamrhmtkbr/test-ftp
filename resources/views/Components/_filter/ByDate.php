<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
?>

<div class="form-group mb-0">
    <label class="mb-0 text-dark text-xs" for="<?= $listParam ?>">
        <?= StringHelper::toCapitalize($listParam) ?>
    </label>
    <input
            type="date"
            name="<?= $listParam ?>"
            id="<?= $listParam ?>"
            class="form-control"
            placeholder="<?= StringHelper::toCapitalize($listParam) ?>"
            onchange="
                    const url = new window.URL(window.location.href);
                    url.searchParams.set('<?= $listParam ?>', this.value);
                    window.history.pushState({}, '', url);
                    "
            value="<?= htmlspecialchars($_GET[$listParam] ?? '') ?>"
    >
</div>
