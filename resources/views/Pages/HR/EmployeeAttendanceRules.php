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
            <h6 class="m-0 font-weight-bold text-primary">Filter Attendance Rules</h6>
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
                    $searchAction = '/hr/employee/attendance-rules';
                    $searchPlaceholder = 'Rule Name...';
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
                                <?php if ($key == 'id') {
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
                                    <?php if ($key == 'id') {
                                        continue;
                                    } ?>
                                    <td <?= ($key != 'rule_name') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'start_time' || $key == 'end_time' || $key == 'late_threshold') {
                                            echo $value !== null ? TimeHelper::getClock($value) : '-';
                                        } else if ($key == 'created_at' || $key == 'updated_at') {
                                            echo $value !== null ? TimeHelper::getTimeBasic($value) : '-';
                                        } else if ($key == 'penalty_for_late') {
                                            echo NumberHelper::convertNumberToRupiah($value);
                                        } else {
                                            echo $value;
                                        } ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap: .5rem">
                                    <a href="#form-update-employee-attendance-rule" class="btn btn-sm btn-primary"
                                       id="button-edit"
                                       data-attendanceruleid="<?= $items['id'] ?>"
                                       data-rulename="<?= $items['rule_name'] ?>"
                                       data-starttime="<?= $items['start_time'] ?>"
                                       data-endtime="<?= $items['end_time'] ?>"
                                       data-latethreshold="<?= $items['late_threshold'] ?>"
                                       data-penaltyforlate="<?= $items['penalty_for_late'] ?>">Edit</a>
                                    <form action=" /hr/employee/attendance-rule" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $items['id'] ?>">
                                        <button onclick="return confirm('Apakah kamu yakin ingin menghapus item ini?')" type="submit" class="btn btn-sm btn-primary">Delete</button>
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

                $floatName = 'success-delete-employee-attendance-rule';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-delete-employee-attendance-rule';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';
                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>
        </div>
    </div>

    <br>
    <br id="form-update-employee-attendance-rule">
    <br>
    <br>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <span id="form-title">Add</span> Employee Attendance Rule
            </h6>
        </div>
        <div class="card-body">
            <form action="/hr/employee/attendance-rule" method="POST">
                <input type="hidden" name="id" id="editId" value="">
                <?php

                $floatName = 'success-update-employee-attendance-rule';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-employee-attendance-rule';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'rule_name';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'start_time';
                $inputType = 'time';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'end_time';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'late_threshold';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'penalty_for_late';
                $inputType = 'number';
                require __DIR__ . '/../../Components/_form/InputText.php';

                ?>
                <br>
                <div class="d-flex" style="gap: .5rem" onclick="location.reload()">
                    <div class="btn btn-sm btn-outline-primary">Cancel</div>
                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
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
            document.querySelector('#editId').setAttribute('value', this.dataset.attendanceruleid);
            document.querySelector('input[name="rule_name"]').setAttribute('value', this.dataset.rulename);
            document.querySelector('input[name="start_time"]').value = this.dataset.starttime;
            document.querySelector('input[name="end_time"]').value = this.dataset.endtime;
            document.querySelector('input[name="late_threshold"]').value = this.dataset.latethreshold;
            document.querySelector('input[name="penalty_for_late"]').setAttribute('value', this.dataset.penaltyforlate);
        });
    });
</script>