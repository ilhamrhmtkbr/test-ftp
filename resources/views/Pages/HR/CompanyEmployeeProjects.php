<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>

<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Employee Projects</h6>
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
                    $searchAction = '/hr/company/employee/projects';
                    $searchPlaceholder = 'Name...';
                    require __DIR__ . '/../../Components/_filter/BySearch.php';
                    ?>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <?php
                            $listName = 'Created At From';
                            $listParam = 'created_at_from';
                            require __DIR__ . '/../../Components/_filter/ByDate.php';
                            ?>
                        </div>
                        <div class="col-md-6 mb-2">
                            <?php
                            $listName = 'Created At Until';
                            $listParam = 'created_at_until';
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
                    $floatName = 'danger-created-at-until';
                    if (isset($_GET['created_at_from']) && (!isset($_GET['created_at_until']))) {
                        $session = [
                                'float' => [
                                        'type' => 'danger',
                                        'message' => 'Created At Until Tidak Boleh Kosong',
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

                    $listName = 'Status';
                    $listParam = 'status';
                    $listItem = ['Ongoing', 'Completed', 'On Hold'];
                    $listValue = ['ongoing', 'completed', 'on-hold'];
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
                                    <td <?= ($key == 'name' || $key == 'description') ? "class='fit'" : '' ?>
                                            <?= ($key == 'status' || $key == 'start_date' || $key == 'end_date' || $key == 'created_at' || $key == 'updated_at') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'start_date' || $key == 'end_date' || $key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTimeBasic($value);
                                        } else {
                                            echo $value;
                                        } ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap:.5rem;">
                                    <a href="#form-update-company-employee-project" class="btn btn-sm btn-primary"
                                       id="button-edit"
                                       data-projectid="<?= $items['id'] ?>"
                                       data-name="<?= $items['name'] ?>"
                                       data-description="<?= $items['description'] ?>"
                                       data-startdate="<?= $items['start_date'] ?>"
                                       data-enddate="<?= $items['end_date'] ?>"
                                       data-status="<?= $items['status'] ?>">Edit</a>
                                    <form action="/hr/company/employee/project" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $items['id'] ?>">
                                        <input type="hidden" name="employee_id" value="<?= $items['name'] ?>">
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

                $floatName = 'success-delete-company-employee-project';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-delete-company-employee-project';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>

        </div>
    </div>

    <br>
    <br id="form-update-company-employee-project">
    <br>
    <br>
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <span id="form-title">Add</span> Company Employee Project
            </h6>
        </div>
        <div class="card-body">
            <form action="/hr/company/employee/project" method="POST">
                <input type="hidden" name="id" id="editId" value="">
                <?php

                $floatName = 'success-update-company-employee-project';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-company-employee-project';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'name';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'description';
                require __DIR__ . '/../../Components/_form/InputTextarea.php';

                $name = 'start_date';
                $inputType = 'date';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'end_date';
                $inputType = 'date';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'status';
                $options = ['ongoing', 'completed', 'on-hold'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

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
            document.querySelector('#editId').setAttribute('value', this.dataset.projectid);
            document.querySelector('input[name="name"]').setAttribute('value', this.dataset.name);
            document.querySelector('textarea[name="description"]').value = this.dataset.description;
            document.querySelector('input[name="start_date"]').setAttribute('value', this.dataset.startdate);
            document.querySelector('input[name="end_date"]').setAttribute('value', this.dataset.enddate);
            document.querySelector('select[name="status"]').value = this.dataset.status;
        });
    });
</script>