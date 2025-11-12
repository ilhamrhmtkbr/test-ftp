<?php
$isActive = isset($_GET[$listParam]);
$selectedLabel = $isActive ? $_GET[$listParam] : $listName;
?>

<!-- Trigger Button -->
<button
        type="button"
        class="btn btn-sm <?= $isActive ? 'btn-primary' : 'btn-outline-primary' ?> filter-data-by-list"
        data-toggle="modal"
        data-target="#filterModal_<?= $listParam ?>"
>
    <i class="fa fa-filter"></i>
    <?= ucfirst(htmlspecialchars($selectedLabel)) ?>
</button>

<!-- Modal -->
<div
        class="modal fade"
        id="filterModal_<?= $listParam ?>"
        tabindex="-1"
        role="dialog"
        aria-labelledby="filterModalLabel_<?= $listParam ?>"
        aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">

            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="filterModalLabel_<?= $listParam ?>">
                    Filter by <?= htmlspecialchars($listName) ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="list-group">
                    <?php for ($i = 0; $i < count($listItem); $i++) : ?>
                        <?php
                        // Buat URL baru tanpa parameter lama dulu biar ga double
                        $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
                        $query = $_GET;
                        $query[$listParam] = $listValue[$i];
                        $url = $baseUrl . '?' . http_build_query($query);
                        ?>
                        <a
                                href="<?= htmlspecialchars($url) ?>"
                                class="list-group-item list-group-item-action <?= ($isActive && $_GET[$listParam] == $listValue[$i]) ? 'active' : '' ?>"
                        >
                            <?= ucfirst(htmlspecialchars($listItem[$i])) ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>
