<?php if (isset($session['badge']) && $badgeName === $session['badge']['name']) : ?>
    <div
            class="alert alert-<?= $session['badge']['type'] === 'success' ? 'success' : ($session['badge']['type'] === 'error' ? 'danger' : 'info') ?> alert-dismissible fade show shadow-sm"
            role="alert"
    >
        <?= htmlspecialchars($session['badge']['message']) ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
