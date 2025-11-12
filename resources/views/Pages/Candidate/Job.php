<?php

use ilhamrhmtkbr\App\Helper\TimeHelper;

?>

    <div class="container-fluid">
        <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

        <!-- Job Information Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Job Information</h6>
            </div>
            <div class="card-body">
                <h4 class="text-primary font-weight-bold mb-3"><?= $data['job_title'] ?></h4>
                <p class="text-gray-800 mb-4"><?= $data['job_description'] ?></p>

                <div class="row">
                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Status
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            <?php
                                            $badgeClass = 'secondary';
                                            $statusLower = strtolower($data['status']);
                                            if ($statusLower == 'open' || $statusLower == 'active') {
                                                $badgeClass = 'success';
                                            } elseif ($statusLower == 'closed') {
                                                $badgeClass = 'danger';
                                            } elseif ($statusLower == 'pending') {
                                                $badgeClass = 'warning';
                                            }
                                            ?>
                                            <span class="badge badge-<?= $badgeClass ?> px-3 py-2">
                                            <?= ucfirst($data['status']) ?>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Created At
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            <?= TimeHelper::getTimeBasic($data['created_at']) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-plus fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-3">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Updated At
                                        </div>
                                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                                            <?= TimeHelper::getTimeBasic($data['updated_at']) ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Information Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="border-left-success shadow h-100 py-3 px-3">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-2">
                                Department Name
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $data['name'] ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8 mb-3">
                        <div class="border-left-success shadow h-100 py-3 px-3">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-2">
                                Description
                            </div>
                            <div class="text-gray-800">
                                <?= $data['description'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apply Button Card -->
        <div class="card shadow mb-4">
            <div class="card-body text-center py-4">
                <form action="/candidate/job" method="POST">
                    <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Apply Now
                    </button>
                </form>
            </div>
        </div>
    </div>

<?php

$floatName = 'success-apply-job';
require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

$floatName = 'error-apply-job';
require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

?>