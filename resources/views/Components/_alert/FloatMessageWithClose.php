<?php if (isset($session['float']) && $floatName === $session['float']['name']) : ?>
    <div class="py-2 px-3 shadow d-flex bg-<?php echo $session['float']['type'] === 'success' ? 'success' : ($session['float']['type'] === 'error' ? 'danger' : 'info') ?> rounded align-items-center"
         style="max-width: 70dvw;
            width: max-content;
            position: fixed;
            gap: 1rem;
            top: 1rem;
            right: 1rem;
            z-index: 100;
            height: max-content;">
        <p class="text-white mb-0"><?= htmlspecialchars($session['float']['message']) ?>
        </p>
        <div class="bg-white text-danger d-flex justify-content-center align-items-center font-weight-bold"
             style="min-width: 21px;
                min-height: 21px;
                max-width: 21px;
                max-height: 21px;
                cursor: pointer;
                border-radius: 21px;" onclick="this.parentElement.remove()">&times;
        </div>
    </div>
<?php endif; ?>