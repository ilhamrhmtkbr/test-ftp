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
                    $searchAction = '/hr/employee/payrolls';
                    $searchPlaceholder = 'Name...';
                    require __DIR__ . '/../../Components/_filter/BySearch.php';
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            $listName = 'Payment Date From';
                            $listParam = 'payment_date_from';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                            $listName = 'Payment Date Until';
                            $listParam = 'payment_date_until';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                    </div>
                    <div class="btn btn-primary col mt-2 btn-sm"
                         onclick="const currUrl = new window.URL(window.location.href);
                                  currUrl.hash = '';
                                  window.location = currUrl.href;">
                        Search
                    </div>
                    <?php
                    $floatName = 'danger-payment-date-until';
                    if (isset($_GET['payment_date_from']) && (!isset($_GET['payment_date_until']))) {
                        $session = [
                                'float' => [
                                        'type' => 'danger',
                                        'message' => 'Payment Date Until Tidak Boleh Kosong',
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

                        $listName = 'Total Overtime';
                        $listParam = 'total_overtime';
                        $listValue = [100000, 200000, 300000, 400000, 500000, 600000];
                        $listItem = array_map(function ($value) {
                            return 'Lebih Dari ' . NumberHelper::convertNumberToRupiah($value);
                        }, $listValue);
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Base Salary';
                        $listParam = 'base_salary';
                        $listValue = [11000000, 12000000, 13000000, 14000000, 15000000];
                        $listItem = array_map(function ($value) {
                            return 'Lebih Dari ' . NumberHelper::convertNumberToRupiah($value);
                        }, $listValue);
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Late Penalties';
                        $listParam = 'late_penalties';
                        $listValue = [10000, 20000, 30000, 40000, 50000, 60000, 70000, 80000];
                        $listItem = array_map(function ($value) {
                            return 'Lebih Dari ' . NumberHelper::convertNumberToRupiah($value);
                        }, $listValue);
                        require __DIR__ . '/../../Components/_filter/ByList.php';

                        $listName = 'Net Salary';
                        $listParam = 'net_salary';
                        $listValue = [11000000, 12000000, 13000000, 14000000, 15000000];
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
                                    <td <?= ($key != 'name') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'payroll_month' || $key == 'payment_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTimeBasic($value);
                                        } elseif ($key == 'base_salary' || $key == 'total_overtime' || $key == 'late_penalties' || $key == 'net_salary') {
                                            echo NumberHelper::convertNumberToRupiah($value);
                                        } else {
                                            echo $value;
                                        } ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap: .5rem;">
                                    <a href="/hr/employee/details?id=<?= $items['email'] ?>" class="btn btn-sm btn-primary" style="margin-right: var(--m);">Detail</a>
                                    <a href="#form-update-employee-payroll" class="btn btn-sm btn-primary"
                                       id="button-edit"
                                       data-payroll_id="<?= $items['id'] ?>"
                                       data-email="<?= $items['email'] ?>"
                                       data-payrollmonth="<?= substr($items['payroll_month'], 0, -3) ?>"
                                       data-basesalary="<?= $items['base_salary'] ?>"
                                       data-status="<?= $items['status'] ?>"
                                       data-paymentdate="<?= $items['payment_date'] ?>"
                                       data-remarks="<?= $items['remarks'] ?>">Edit</a>
                                    <form action=" /hr/employee/payroll" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $items['id'] ?>">
                                        <button onclick="return confirm('Apakah kamu yakin ingin menghapus item ini?')"
                                                type="submit"
                                                class="btn btn-sm btn-primary">Delete</button>
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
    <br>
    <br id="form-update-employee-payroll">
    <br>
    <br>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <span id="form-title">Add</span> Employee Payroll
            </h6>
        </div>
        <div class="card-body">
            <form action="/hr/employee/payroll" method="POST">
                <input type="hidden" name="id" id="editId" value="">
                <?php

                $floatName = 'success-update-employee-payroll';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-employee-payroll';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'email';
                $inputType = 'email';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'payroll_month';
                $inputType = 'month';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'base_salary';
                $inputType = 'number';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'status';
                $options = ['Pending', 'Paid'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                $name = 'payment_date';
                $inputType = 'date';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'remarks';
                $inputType = 'text';
                require __DIR__ . '/../../Components/_form/InputText.php';

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
        button.addEventListener('click', function() {
            document.getElementById('form-title').innerHTML = 'Update';
            document.querySelector('#editId').setAttribute('value', this.dataset.payroll_id);
            document.querySelector('input[name="email"]').setAttribute('value', this.dataset.email);
            document.querySelector('input[name="email"]').readOnly = true;
            document.querySelector('input[name="payroll_month"]').setAttribute('value', this.dataset.payrollmonth);
            document.querySelector('input[name="base_salary"]').setAttribute('value', this.dataset.basesalary);
            document.querySelector('select[name="status"]').setAttribute('value', this.dataset.status);
            document.querySelector('input[name="payment_date"]').setAttribute('value', this.dataset.paymentdate);
            document.querySelector('input[name="remarks"]').setAttribute('value', this.dataset.remarks);
        });
    });
</script>