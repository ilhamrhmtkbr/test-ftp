<?php

use ilhamrhmtkbr\App\Helper\UrlHelper;

$totalPages = $data['total-page'];
$page = $_GET['page'] ?? 1;
$keyword = $_GET['keyword'] ?? '';
$orderBy = $_GET['orderBy'] ?? 'DESC';

$prev = max(1, $page - 1);
$next = min($totalPages, $page + 1);
?>

<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">

        <!-- Tombol Prev -->
        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= UrlHelper::getPathInfo() ?>?keyword=<?= $keyword ?>&orderBy=<?= $orderBy ?>&page=<?= $prev ?>" tabindex="-1">Previous</a>
        </li>

        <!-- Daftar Halaman -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="<?= UrlHelper::getPathInfo() ?>?keyword=<?= $keyword ?>&orderBy=<?= $orderBy ?>&page=<?= $i ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <!-- Tombol Next -->
        <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= UrlHelper::getPathInfo() ?>?keyword=<?= $keyword ?>&orderBy=<?= $orderBy ?>&page=<?= $next ?>">Next</a>
        </li>

    </ul>
</nav>
