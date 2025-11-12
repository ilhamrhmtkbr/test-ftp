<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>

<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Attendance</h6>
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
            <div class="d-flex" style="gap: .5rem">
                <?php
                $listName = 'Sort';
                $listParam = 'orderBy';
                $listItem = ['old', 'new'];
                $listValue = ['ASC', 'DESC'];
                require __DIR__ . '/../../Components/_filter/ByList.php';

                $listName = 'Status';
                $listParam = 'status';
                $listValue = ['Present', 'Late', 'Absent', 'On Leave'];
                $listItem = $listValue;
                require __DIR__ . '/../../Components/_filter/ByList.php';
                ?>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data</h6>
        </div>
        <div class="card-body">
            <?php

            $floatName = 'success-attendance-check';
            require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

            $floatName = 'error-attendance-check';
            require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

            ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="card p-3" style="width: max-content; height: max-content;">
                        <img src="/assets/employee-check.png" style="width: 150px; height: 150px;">
                        <a href="/assets/employee-check.png" download="employee-check.png"
                           class="btn btn-outline-primary btn-sm">Download Qr Code</a>
                        <button type="button"
                                class="btn btn-primary btn-sm mt-2"
                                data-toggle="modal"
                                data-target="#staticBackdrop">
                            Scan Sekarang
                        </button>
                    </div>
                </div>
                <div class="col-md-9">
                    <?php if (count($data['results']) > 0) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>No.</th>
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
                                            <td style='text-align:center;'>
                                                <?php if ($key == 'attendance_date' || $key == 'created_at' || $key == 'updated_at') {
                                                    echo $value !== null ? TimeHelper::getTimeBasic($value) : '-';
                                                } elseif ($key == 'check_in_time' || $key == 'check_out_time') {
                                                    echo $value !== null ? TimeHelper::getClock($value) : '-';
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
    </div>
</div>

<div class="modal fade"
     id="staticBackdrop"
     data-backdrop="static"
     data-keyboard="false"
     tabindex="-1"
     role="dialog"
     aria-labelledby="staticBackdropLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="reader" style="width: 100%;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script src="/assets/js/Employee/html5-qrcode.min.js"></script>

<script>
    let scannerStarted = false;

    function startScanning() {
        if (scannerStarted) {
            return;
        }

        scannerStarted = true;

        try {
            const html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: {width: 212, height: 212}
            });

            html5QrcodeScanner.render(
                (decodedText) => {
                    location.href = decodedText;
                },
                (error) => {

                }
            );
        } catch (error) {
            scannerStarted = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const scanButton = document.querySelector('[data-target="#staticBackdrop"]');

        if (scanButton) {
            scanButton.addEventListener('click', function () {
                setTimeout(startScanning, 300);
            });
        }
    });
</script>