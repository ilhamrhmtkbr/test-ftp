<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>

<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Project Assignments</h6>
            <div class="d-flex" style="gap: .5rem">
                <div onclick="downloadPdf()"
                     class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-download"></i> Download Pdf
                </div>
                <a href="<?= UrlHelper::getPathInfo() ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <?php
                            $listName = 'Assigned Date From';
                            $listParam = 'assigned_date_from';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                        <div class="col-md-6 mb-2">
                            <?php
                            $listName = 'Assigned Date Until';
                            $listParam = 'assigned_date_until';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                    </div>
                    <div class="btn btn-sm btn-outline-primary col"
                         onclick="const currUrl = new window.URL(window.location.href);
                                  currUrl.hash = '';
                                  window.location = currUrl.href;">
                        Search
                    </div>
                    <?php
                    $floatName = 'danger-assigned-date-until';
                    if (isset($_GET['assigned_date_from']) && (!isset($_GET['assigned_date_until']))) {
                        $session = [
                                'float' => [
                                        'type' => 'danger',
                                        'message' => 'Assigned Date Until Tidak Boleh Kosong',
                                        'name' => $floatName
                                ]
                        ];
                    }
                    require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';
                    ?>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex flex-wrap" style="gap: .5rem">
                        <?php
                        $listName = 'Sort';
                        $listParam = 'orderBy';
                        $listItem = ['old', 'new'];
                        $listValue = ['ASC', 'DESC'];
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Status';
                        $listParam = 'status';
                        $listValue = ['ongoing', 'completed', 'on-hold'];
                        $listItem = ['Ongoing', 'Completed', 'On Hold'];
                        require __DIR__ . '/../../Components/_filter/ByList.php';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data</h6>
        </div>
        <div class="card-body">
            <?php if (count($data['results']) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No. </th>
                            <?php foreach (array_keys((array)$data['results'][0]) as $key): ?>
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
                                    <td <?= ($key != 'name') ? "style='text-align:center;'" : '' ?>
                                            <?= ($key == 'name' || $key == 'description') ? "class='fit'" : '' ?>>
                                        <?php if ($key == 'start_date' || $key == 'end_date' || $key == 'assigned_date') {
                                            echo TimeHelper::getTimeBasic($value);
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

                <?php
                $data['total-page'] = $data['total-page'];
                require __DIR__ . '/../../Components/Pagination.php';

                $floatName = 'success-delete-employee-payroll';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-delete-employee-payroll';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>
        </div>
    </div>

</div>