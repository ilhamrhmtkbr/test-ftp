<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
?>

<div class="card mb-4">
    <div class="card-body">

        <!-- Image Preview -->
        <div class="row mb-3 text-center">
            <div class="col-md-6 mb-3 mb-md-0">
                <small class="d-block font-weight-bold text-muted mb-2">Your Image</small>
                <img
                        src="<?= isset($urlImage) && $urlImage != '/assets/img/' ? $urlImage : '/assets/img/no-image.png' ?>"
                        alt="old-image"
                        class="img-thumbnail rounded shadow-sm"
                        style="max-width: 200px;"
                        id="imageOld"
                >
            </div>
            <div class="col-md-6">
                <small class="d-block font-weight-bold text-muted mb-2">New Image</small>
                <img
                        src=""
                        alt="new-image"
                        class="img-thumbnail rounded shadow-sm"
                        style="max-width: 200px; display: none;"
                        id="imagePreview"
                >
            </div>
        </div>

        <!-- Upload Input -->
        <div class="form-group">
            <label for="imageInput" class="font-weight-bold"><?= StringHelper::toCapitalize($name) ?></label>
            <input
                    type="file"
                    class="form-control-file <?= isset($session['errors'][$name]) ? 'is-invalid' : '' ?>"
                    name="<?= $name ?>"
                    id="imageInput"
                    accept="image/*"
            >
            <?php if (isset($session['errors'][$name])) : ?>
                <div class="invalid-feedback d-block">
                    <?= $session['errors'][$name][0] ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function (e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'inline-block';
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });
</script>