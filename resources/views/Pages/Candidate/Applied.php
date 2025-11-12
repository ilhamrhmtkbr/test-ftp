<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>

    <div class="container-fluid">
        <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Filter Applied Jobs</h6>
                <a href="<?= UrlHelper::getPathInfo() ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <?php
                        $searchAction = '/candidate/applied';
                        $searchPlaceholder = 'Institution...';
                        require __DIR__ . '/../../Components/_filter/BySearch.php';
                        ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <?php
                        $listName = 'Sort';
                        $listParam = 'orderBy';
                        $listItem = ['old', 'new'];
                        $listValue = ['ASC', 'DESC'];
                        require __DIR__ . '/../../Components/_filter/ByList.php';
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (count($data['results']) > 0) : ?>
            <!-- Data Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Applied Jobs List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <?php foreach (array_keys((array)$data['results'][0]) as $key): ?>
                                    <?php if ($key == 'id') {
                                        continue;
                                    } ?>
                                    <th><?= StringHelper::toCapitalize($key) ?></th>
                                <?php endforeach ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $counter = 1;
                            foreach ($data['results'] as $items) : ?>
                                <tr>
                                    <td><?= $counter++ ?></td>
                                    <?php foreach ($items as $key => $value) : ?>
                                        <?php if ($key == 'id') {
                                            continue;
                                        } ?>
                                        <td <?= ($key == 'job_description') ? "class='fit'" : '' ?>
                                                <?= ($key == 'status' || $key == 'created_at' || $key == 'updated_at') ? "style='text-align:center;'" : '' ?>>
                                            <?php if ($key == 'created_at' || $key == 'updated_at') {
                                                echo TimeHelper::getTimeBasic($value);
                                            } elseif ($key == 'status') {
                                                $badgeClass = 'secondary';
                                                $statusLower = strtolower($value);
                                                if ($statusLower == 'approved' || $statusLower == 'accepted') {
                                                    $badgeClass = 'success';
                                                } elseif ($statusLower == 'pending' || $statusLower == 'review') {
                                                    $badgeClass = 'warning';
                                                } elseif ($statusLower == 'rejected' || $statusLower == 'declined') {
                                                    $badgeClass = 'danger';
                                                }
                                                echo "<span class='badge badge-{$badgeClass}'>{$value}</span>";
                                            } else {
                                                echo $value;
                                            } ?>
                                        </td>
                                    <?php endforeach ?>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card shadow">
                <div class="card-body">
                    <?php
                    $data['total-page'] = $data['total-page'];
                    require __DIR__ . '/../../Components/Pagination.php';
                    ?>
                </div>
            </div>
        <?php else : ?>
            <!-- No Results -->
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-danger h5">No applications found</p>
                    <p class="text-muted">You haven't applied to any jobs yet</p>
                </div>
            </div>
        <?php endif ?>
    </div>

<?php require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php'; ?>