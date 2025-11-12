<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\NumberHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>
<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Explore</h6>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center" style="gap: .5rem">
                <a href="/hr/employee/attendance" class="btn btn-sm btn-primary">
                    Attendance
                </a>
                <a href="/hr/employee/attendance-rules" class="btn btn-sm btn-primary">
                    Attendance Rules
                </a>
                <a href="/hr/employee/contracts" class="btn btn-sm btn-primary">
                    Contracts
                </a>
                <a href="/hr/employee/leave-requests" class="btn btn-sm btn-primary">
                    Leave Request
                </a>
                <a href="/hr/employee/overtime" class="btn btn-sm btn-primary">
                    Overtime
                </a>
                <a href="/hr/employee/payrolls" class="btn btn-sm btn-primary">
                    Payrolls
                </a>
                <a href="/hr/employee/project-assignments" class="btn btn-sm btn-primary">
                    Projects Assignments
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Employees</h6>
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
                    $searchAction = '/hr/employees';
                    $searchPlaceholder = 'Name...';
                    require __DIR__ . '/../../Components/_filter/BySearch.php';
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

                        $listName = 'Roles';
                        $listParam = 'role';
                        $listItem = array_values($data['employee_roles']);
                        $listValue = $listItem;
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Departments';
                        $listParam = 'department';
                        $listItem = array_values($data['company_departments']);
                        $listValue = $listItem;
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Status';
                        $listParam = 'status';
                        $listItem = ['Active', 'Inactive'];
                        $listValue = $listItem;
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Salary';
                        $listParam = 'salary';
                        $listValue = [10000000, 11000000, 12000000, 13000000, 14000000, 15000000];
                        $listItem = array_map(function ($value) {
                            return 'Lebih Dari ' . NumberHelper::convertNumberToRupiah($value);
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
            <?php if (count($data['employees']['results']) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys((array)$data['employees']['results'][0]) as $key): ?>
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
                        foreach ($data['employees']['results'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <?php if ($key == 'id') {
                                        continue;
                                    } ?>
                                    <td <?= ($key == 'role' || $key == 'status' || $key == 'hire_date') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'created_at') {
                                            echo TimeHelper::getTimeBasic($value);
                                        } elseif ($key == 'hire_date') {
                                            echo TimeHelper::getTime($value);
                                        } elseif ($key == 'salary') {
                                            echo NumberHelper::convertNumberToRupiah($value);
                                        } else {
                                            echo $value;
                                        } ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap: .5rem;">
                                    <a href="/hr/employee/details?id=<?= $items['email'] ?>"
                                       class="btn btn-sm btn-primary">Detail</a>
                                    <a href="#form-update-employee" class="btn btn-sm btn-primary"
                                       id="button-edit"
                                       data-email="<?= $items['email'] ?>"
                                       data-role="<?= array_search($items['role'], $data['employee_roles']) ?>"
                                       data-department="<?= array_search($items['department'], $data['company_departments']) ?>"
                                       data-salary="<?= $items['salary'] ?>"
                                       data-hiredate="<?= $items['hire_date'] ?>"
                                       data-status="<?= $items['status'] ?>">Edit</a>
                                    <form action="/hr/employee" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="user_id" value="<?= $items['email'] ?>">
                                        <button onclick="return confirm('Apakah kamu yakin ingin menghapus item ini?')"
                                                type="submit" class="btn btn-sm btn-primary">Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                <?php
                $data['total-page'] = $data['employees']['total-page'];
                require __DIR__ . '/../../Components/Pagination.php'
                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>
        </div>
    </div>

    <?php
    $floatName = 'success-delete-employee';
    require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

    $floatName = 'error-delete-employee';
    require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';
    ?>

    <br>
    <br id="form-update-employee">
    <br>
    <br>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <span>Add</span> Employee
            </h6>
        </div>
        <div class="card-body">
            <form action="/hr/employee" method="POST">
                <?php

                $floatName = 'success-update-employee';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-employee';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'email';
                $inputType = 'email';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'role';
                $options = $data['employee_roles'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                $name = 'department';
                $options = $data['company_departments'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                $name = 'salary';
                $inputType = 'number';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'hire_date';
                $inputType = 'date';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'status';
                $options = ['active', 'inactive'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                ?>
                <br>
                <div class="d-flex" style="gap: .5rem" onclick="location.reload()">
                    <div class="btn btn-sm btn-outline-primary">Cancel</div>
                    <button style="place-self:center" type="submit" class="btn btn-sm btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const buttonEdit = document.querySelectorAll('#button-edit');
    buttonEdit.forEach((button) => {
        button.addEventListener('click', function () {
            document.getElementsByTagName('span')[0].innerHTML = 'Update';
            document.querySelector('input[name="email"]').setAttribute('value', this.dataset.email);
            document.querySelector('input[name="email"]').readOnly = true;
            document.querySelector('select[name="role"]').value = this.dataset.role;
            document.querySelector('select[name="department"]').value = this.dataset.department;
            document.querySelector('input[name="salary"]').value = this.dataset.salary;
            document.querySelector('input[name="hire_date"]').setAttribute('value', this.dataset.hiredate);
            document.querySelector('select[name="status"]').value = this.dataset.status;
        });
    });
</script>