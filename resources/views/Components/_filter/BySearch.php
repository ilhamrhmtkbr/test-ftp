<form action="<?= $searchAction ?>" method="GET" class="form-inline mb-3">
    <div class="input-group" style="width: 100% !important;">
        <input
                type="search"
                name="keyword"
                class="form-control bg-light border-0 small"
                placeholder="Search by <?= htmlspecialchars($searchPlaceholder) ?>"
                value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                aria-label="Search"
                aria-describedby="button-search"
        >
        <div class="input-group-append">
            <button class="btn btn-primary" type="submit" id="button-search">
                <i class="fas fa-search fa-sm"></i> <!-- pakai ikon Font Awesome dari SB Admin 2 -->
            </button>
        </div>
    </div>
</form>