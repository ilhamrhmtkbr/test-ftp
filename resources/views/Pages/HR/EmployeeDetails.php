<?php

use ilhamrhmtkbr\App\Helper\NumberHelper;
use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;

if (!isset($_GET['id'])) {
    header('Location: ' . '/hr/employees');
    exit;
}
?>

<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <form class="col-md-6">
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" value="<?= $data['employee_data'][0]['name'] ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" value="<?= $data['employee_data'][0]['email'] ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <input class="form-control" value="<?= $data['employee_data'][0]['role'] ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input class="form-control" value="<?= $data['employee_data'][0]['department'] ?>" readonly/>
                    </div>
                </form>
                <form class="col-md-6">
                    <div class="form-group">
                        <label>Salary</label>
                        <input class="form-control"
                               value="<?= NumberHelper::convertNumberToRupiah($data['employee_data'][0]['salary']) ?>"
                               readonly/>
                    </div>
                    <div class="form-group">
                        <label>Hire Date</label>
                        <input class="form-control"
                               value="<?= TimeHelper::getTime($data['employee_data'][0]['hire_date']) ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <input class="form-control" value="<?= $data['employee_data'][0]['status'] ?>" readonly/>
                    </div>
                    <div class="form-group">
                        <label>Created At</label>
                        <input class="form-control"
                               value="<?= TimeHelper::getTime($data['employee_data'][0]['created_at']) ?>" readonly/>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card shadow mt-4">
        <div class="card-body">
            <h5>Attendance</h5>
            <?php if ($data['employee_attendance']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['employee_attendance'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['employee_attendance'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td style="text-align:center">
                                        <?php if ($key == 'attendance_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo $value != null ? TimeHelper::getTime($value) : '-';
                                        } elseif ($key == 'check_in_time' || $key == 'check_out_time') {
                                            echo $value != null ? TimeHelper::getClock($value) : '-';
                                        } else {
                                            echo $value;
                                        }
                                        ?></td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-danger text-center mt-4"> Employee belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
            <h5>Contracts</h5>

            <?php if ($data['employee_contracts']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['employee_contracts'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['employee_contracts'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td style="text-align:center;"
                                            <?= $key == 'contract_terms' ? "class='fit'" : '' ?>>
                                        <?php if ($key == 'contract_start_date' || $key == 'contract_end_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTime($value);
                                        } else if ($key == 'salary') {
                                            echo NumberHelper::convertNumberToRupiah($value);
                                        } else {
                                            echo $value;
                                        }
                                        ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-danger text-center mt-4"> Employee belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
            <h5>Leave Requests</h5>
            <?php if ($data['employee_leave_request']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['employee_leave_request'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['employee_leave_request'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td <?= $key != 'remarks' ? "style='text-align:center'" : '' ?>>
                                        <?php if ($key == 'start_date' || $key == 'end_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTime($value);
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
            <?php else : ?>
                <p class="text-danger text-center mt-4"> Employee belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
            <h5>Overtime</h5>
            <?php if ($data['employee_overtime']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['employee_overtime'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['employee_overtime'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td <?= $key != 'remarks' ? "style='text-align:center'" : '' ?>>
                                        <?php if ($key == 'overtime_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTime($value);
                                        } else if ($key == 'start_time' || $key == 'end_time') {
                                            echo $value != null ? TimeHelper::getClock($value) : '-';
                                        } else if ($key == 'overtime_rate' || $key == 'total_payment') {
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
            <?php else : ?>
                <p class="text-danger text-center mt-4"> Employee belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
            <h5>Payrolls</h5>
            <?php if ($data['employee_payrolls']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['employee_payrolls'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['employee_payrolls'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td <?= $key != 'remarks' ? "style='text-align:center'" : '' ?>>
                                        <?php if ($key == 'payroll_month' || $key == 'payment_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTime($value);
                                        } else if ($key == 'base_salary' || $key == 'total_overtime' || $key == 'late_penalties' || $key == 'net_salary') {
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
            <?php else : ?>
                <p class="text-danger text-center mt-4"> Employee belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-body">
        <h5>Project Assignments</h5>
            <?php if ($data['employee_project_assignments']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['employee_project_assignments'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['employee_project_assignments'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td <?= $key != 'remarks' ? "style='text-align:center'" : '' ?>
                                            <?= ($key != 'name' || $key != 'description') ? "class='fit'" : '' ?>>
                                        <?php if ($key == 'start_date' || $key == 'end_date' || $key == 'assigned_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTime($value);
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
            <?php else : ?>
                <p class="text-danger text-center mt-4"> Employee belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>
</div>