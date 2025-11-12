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
            <h6 class="m-0 font-weight-bold text-primary">Filter Jobs</h6>
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
                    <?php
                    $searchAction = '/hr/employee/overtime';
                    $searchPlaceholder = 'Name...';
                    require __DIR__ . '/../../Components/_filter/BySearch.php';
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $listName = 'Overtime Date From';
                            $listParam = 'overtime_date_from';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            $listName = 'Overtime Date Until';
                            $listParam = 'overtime_date_until';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                    </div>
                    <div class="btn btn-sm btn-primary col mt-2"
                         onclick="const currUrl = new window.URL(window.location.href);
                                  currUrl.hash = '';
                                  window.location = currUrl.href;">
                        Search
                    </div>
                    <?php
                    $floatName = 'danger-overtime-date-until';
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
                                <?php if ($key == 'id' || $key == 'email') {
                                    continue;
                                } ?>
                                <th><?= StringHelper::toCapitalize($key) ?></th>
                            <?php endforeach ?>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['results'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <?php if ($key == 'id' || $key == 'email') {
                                        continue;
                                    } ?>
                                    <td <?= $key == 'remarks' ? "class='fit'" : '' ?>
                                            <?= ($key != 'name' || $key != 'remarks') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'overtime') {
                                            echo $value !== null ? TimeHelper::getTimeBasic($value) : '-';
                                        } else if ($key == 'start_time' || $key == 'end_time') {
                                            echo $value !== null ? TimeHelper::getClock($value) : '-';
                                        } else if ($key == 'overtime_rate' || $key == 'total_payment') {
                                            echo $value !== null ? NumberHelper::convertNumberToRupiah($value) : '-';
                                        } else {
                                            echo $value;
                                        } ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap: .5rem">
                                    <a href="#form-update-employee-overtime" class="btn btn-primary btn-sm"
                                       id="button-edit"
                                       data-overtimeid="<?= $items['id'] ?>"
                                       data-employeeid="<?= $items['email'] ?>"
                                       data-overtimedate="<?= $items['overtime_date'] ?>"
                                       data-starttime="<?= $items['start_time'] ?>"
                                       data-endtime="<?= $items['end_time'] ?>"
                                       data-overtimerate="<?= $items['overtime_rate'] ?>"
                                       data-remarks="<?= $items['remarks'] ?>">Edit</a>
                                    <form action="/hr/employee/overtime" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $items['id'] ?>">
                                        <button onclick="return confirm('Apakah kamu yakin ingin menghapus item ini?')"
                                                type="submit"
                                                class="btn btn-primary btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                <?php
                $data['total-page'] = $data['total-page'];
                require __DIR__ . '/../../Components/Pagination.php';

                $floatName = 'success-delete-employee-overtime';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-delete-employee-overtime';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';
                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>
        </div>
    </div>

    <br>
    <br id="form-update-employee-overtime">
    <br>
    <br>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <span id="form-title">Add</span> Employee Overtimes
            </h6>
        </div>
        <div class="card-body">
            <form action="/hr/employee/overtime" method="POST">
                <input type="hidden" name="id" id="editId" value="">
                <?php

                $floatName = 'success-update-employee-overtime';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-employee-overtime';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'employee_id';
                $inputType = 'email';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'overtime_date';
                $inputType = 'date';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'start_time';
                $inputType = 'time';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'end_time';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'overtime_rate';
                $inputType = 'number';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'remarks';
                $inputType = 'text';
                require __DIR__ . '/../../Components/_form/InputText.php';

                ?>
                <br>
                <div class="d-flex" style="gap: .5rem" onclick="location.reload()">
                    <div class="btn btn-outline-primary btn-sm">Cancel</div>
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const buttonEdit = document.querySelectorAll('#button-edit');
    buttonEdit.forEach((button) => {
        button.addEventListener('click', function() {
            document.getElementById('form-title').innerHTML = 'Update';
            document.querySelector('#editId').setAttribute('value', this.dataset.overtimeid);
            document.querySelector('input[name="employee_id"]').setAttribute('value', this.dataset.employeeid);
            document.querySelector('input[name="employee_id"]').readOnly = true;
            document.querySelector('input[name="overtime_date"]').setAttribute('value', this.dataset.overtimedate);
            document.querySelector('input[name="start_time"]').setAttribute('value', this.dataset.starttime);
            document.querySelector('input[name="end_time"]').value = this.dataset.endtime;
            document.querySelector('input[name="overtime_rate"]').value = this.dataset.overtimerate;
            document.querySelector('input[name="remarks"]').value = this.dataset.remarks;
        });
    });
</script>