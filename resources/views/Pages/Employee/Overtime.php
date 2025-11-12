<?php

use ilhamrhmtkbr\App\Helper\NumberHelper;
use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>

<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Overtime</h6>
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
                            $listName = 'Overtime Date From';
                            $listParam = 'overtime_date_from';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                        <div class="col-md-6 mb-2">
                            <?php
                            $listName = 'Overtime Date Until';
                            $listParam = 'overtime_date_until';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                    </div>
                    <?php
                    $floatName = 'overtime-date-until';
                    if (isset($_GET['overtime_date_from']) && (!isset($_GET['overtime_date_until']))) {
                        $session = [
                                'float' => [
                                        'type' => 'danger',
                                        'message' => 'Overtime Date Until Tidak Boleh Kosong',
                                        'name' => $floatName
                                ]
                        ];
                    }
                    require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';
                    ?>

                    <div class="btn btn-sm btn-outline-primary col" onclick="
                const currUrl = new window.URL(window.location.href);
                currUrl.hash = '';
                window.location = currUrl.href;
            ">
                        Search
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="d-flex flex-wrap" style="gap:.5rem">
                        <?php
                        $listName = 'Sort';
                        $listParam = 'orderBy';
                        $listItem = ['old', 'new'];
                        $listValue = ['ASC', 'DESC'];
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Total Hours';
                        $listParam = 'total_hours';
                        $listItem = [1, 2, 3, 4, 5, 6, 7];
                        $listValue = $listItem;
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Overtime Rate';
                        $listParam = 'overtime_rate';
                        $listValue = [10000, 20000, 30000, 40000, 50000, 60000, 70000, 80000, 90000];
                        $listItem = array_map(function ($value) {
                            return "Lebih dari " . NumberHelper::convertNumberToRupiah($value);
                        }, $listValue);
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Total Payments';
                        $listParam = 'total_payment';
                        $listValue = [50000, 60000, 70000, 80000, 90000, 100000, 150000, 200000, 250000, 300000];
                        $listItem = array_map(function ($value) {
                            return "Lebih dari " . NumberHelper::convertNumberToRupiah($value);
                        }, $listValue);
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
                                    <td <?= ($key != 'remarks') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'overtime_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTimeBasic($value);
                                        } elseif ($key == 'start_time' || $key == 'end_time') {
                                            echo $value != null ? TimeHelper::getClock($value) : '-';
                                        } elseif ($key == 'overtime_rate' || $key == 'total_payment') {
                                            echo NumberHelper::convertNumberToRupiah($value);
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