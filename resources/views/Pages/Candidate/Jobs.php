<?php

use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>

    <div class="container-fluid">
        <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Filter Jobs</h6>
                <a href="<?= UrlHelper::getPathInfo() ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <?php
                        $searchAction = '/candidate/jobs';
                        $searchPlaceholder = 'Job Title';
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
            <!-- Jobs Grid -->
            <div class="row">
                <?php foreach ($data['results'] as $result) : ?>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="flex-grow-1">
                                    <h5 class="card-title text-primary font-weight-bold mb-3">
                                        <?= $result->job_title ?>
                                    </h5>
                                    <p class="card-text text-gray-800 mb-3">
                                        <?= $result->job_description ?>
                                    </p>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge badge-<?= $result->status === 'open' ? 'success' : ($result->status === 'closed' ? 'danger' : 'warning') ?> px-3 py-2">
                                        <?= ucfirst($result->status) ?>
                                    </span>
                                        <small class="text-muted">
                                            <i class="far fa-clock"></i>
                                            <?= TimeHelper::getTimeAgo($result->updated_at) ?>
                                        </small>
                                    </div>
                                    <a href="/candidate/job?id=<?= $result->id ?>"
                                       class="btn btn-primary btn-block btn-sm">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

            <!-- Pagination -->
            <div class="card shadow">
                <div class="card-body">
                    <?php require __DIR__ . '/../../Components/Pagination.php' ?>
                </div>
            </div>
        <?php else : ?>
            <!-- No Results -->
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-3x text-gray-300 mb-3"></i>
                    <p class="text-danger h5">No jobs found</p>
                    <p class="text-muted">Try adjusting your filters</p>
                </div>
            </div>
        <?php endif ?>
    </div>

<?php require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php'; ?>