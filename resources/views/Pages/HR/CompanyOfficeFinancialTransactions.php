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
            <h6 class="m-0 font-weight-bold text-primary">Filter Financial Transactions</h6>
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
                    $searchAction = '/hr/company/office/financial-transactions';
                    $searchPlaceholder = 'Description...';
                    require __DIR__ . '/../../Components/_filter/BySearch.php';
                    ?>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <?php
                            $listName = 'Transaction date From';
                            $listParam = 'transaction_date_from';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                        <div class="col-md-6 mb-2">
                            <?php
                            $listName = 'Transaction date Until';
                            $listParam = 'transaction_date_until';
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
                    $floatName = 'danger-transaction-date-until';
                    if (isset($_GET['transaction_date_from']) && (!isset($_GET['transaction_date_until']))) {
                        $session = [
                                'float' => [
                                        'type' => 'danger',
                                        'message' => 'Transaction date Until Tidak Boleh Kosong',
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

                    $listName = 'Type';
                    $listParam = 'type';
                    $listItem = ['Income', 'Expense'];
                    $listValue = ['income', 'expense'];
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
                                    <td <?= ($key == 'description') ? "class='fit'" : '' ?>
                                            <?= ($key == 'transaction_date' || $key == 'created_at' || $key == 'updated_at') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'transaction_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTimeBasic($value);
                                        } else if ($key == 'amount') {
                                            echo NumberHelper::convertNumberToRupiah($value);
                                        } else {
                                            echo $value;
                                        } ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap:.5rem;">
                                    <a href="#form-update-company-office-financial-transaction" class="btn-sm btn btn-primary"
                                       id="button-edit"
                                       data-projectid="<?= $items['id'] ?>"
                                       data-type="<?= $items['type'] ?>"
                                       data-amount="<?= $items['amount'] ?>"
                                       data-transactiondate="<?= $items['transaction_date'] ?>"
                                       data-description="<?= $items['description'] ?>">Edit</a>
                                    <form action="/hr/company/office/financial-transaction" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $items['id'] ?>">
                                        <button onclick="return confirm('Apakah kamu yakin ingin menghapus item ini?')" type="submit"
                                                class="btn-sm btn btn-primary">Delete</button>
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

                $floatName = 'success-delete-company-office-financial-transaction';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-delete-company-office-financial-transaction';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>
        </div>
    </div>

    <br>
    <br id="form-update-company-office-financial-transaction">
    <br>
    <br>
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <span id="form-title">Add</span> Financial Transactions
            </h6>
        </div>
        <div class="card-body">
            <form action="/hr/company/office/financial-transaction" method="POST">
                <input type="hidden" name="id" id="editId" value="">
                <?php

                $floatName = 'success-update-company-office-financial-transaction';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-company-office-financial-transaction';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'type';
                $options = ['income', 'expense'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                $name = 'amount';
                $inputType = 'number';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'transaction_date';
                $inputType = 'date';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'description';
                require __DIR__ . '/../../Components/_form/InputTextarea.php';

                ?>
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
            document.querySelector('#editId').setAttribute('value', this.dataset.projectid);
            document.querySelector('select[name="type"]').value = this.dataset.type;
            document.querySelector('input[name="amount"]').setAttribute('value', this.dataset.amount);
            document.querySelector('input[name="transaction_date"]').setAttribute('value', this.dataset.transactiondate);
            document.querySelector('textarea[name="description"]').value = this.dataset.description;
        });
    });
</script>