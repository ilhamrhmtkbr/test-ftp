<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>

<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Recruitment</h6>
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
                    $searchAction = '/hr/company/office/recruitments';
                    $searchPlaceholder = 'Name...';
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

                    $listName = 'Status';
                    $listParam = 'status';
                    $listItem = ['Open', 'Closed'];
                    $listValue = ['open', 'closed'];
                    require __DIR__ . '/../../Components/_filter/ByList.php';

                    $listName = 'Departments';
                    $listParam = 'department';
                    $listItem = array_values($data['company_office_departments']);
                    $listValue = $listItem;
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
            <?php if (count($data['company_office_recruitments']['results']) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No. </th>
                            <?php foreach (array_keys($data['company_office_recruitments']['results'][0]) as $key): ?>
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
                        foreach ($data['company_office_recruitments']['results'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <?php if ($key == 'id') {
                                        continue;
                                    } ?>
                                    <td <?= $key == 'job_description' ? "class='fit'" : '' ?>
                                            <?= ($key == 'status' || $key == 'created_at' || $key == 'updated_at') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'created_at' || $key == 'updated_at') {
                                            echo TimeHelper::getTimeBasic($value);
                                        } else {
                                            echo $value;
                                        } ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap:.5rem;">
                                    <a href="#form-update-company-office-recruitment" class="btn-sm btn btn-primary"
                                       id="button-edit"
                                       data-jobid="<?= $items['id'] ?>"
                                       data-jobtitle="<?= $items['job_title'] ?>"
                                       data-jobdepartment="<?= array_search($items['department_name'], $data['company_office_departments']) ?>"
                                       data-jobdescription="<?= $items['job_description'] ?>"
                                       data-jobstatus="<?= $items['status'] ?>">Edit</a>
                                    <form action=" /hr/company/office/recruitment" method="POST">
                                        <input type="hidden" name="_method" value="DELETE" />
                                        <input type="hidden" name="id" value="<?= $items['id'] ?>" />
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
                $data['total-page'] = $data['company_office_recruitments']['total-page'];
                require __DIR__ . '/../../Components/Pagination.php';

                $floatName = 'success-delete-company-office-recruitment';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-delete-company-office-recruitment';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>
        </div>
    </div>

    <br>
    <br id="form-update-company-office-recruitment">
    <br>
    <br>
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <span id="form-title">Add</span> Company Office Recruitment
            </h6>
        </div>
        <div class="card-body">
            <form action="/hr/company/office/recruitment" method="POST">
                <input type="hidden" name="id" id="editId" value="">
                <?php

                $floatName = 'success-update-company-office-recruitment';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-company-office-recruitment';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'job_title';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'department';
                $options = $data['company_office_departments'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                $name = 'job_description';
                require __DIR__ . '/../../Components/_form/InputTextarea.php';

                $name = 'status';
                $options = ['open', 'closed'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

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
            document.querySelector('#editId').setAttribute('value', this.dataset.jobid);
            document.querySelector('input[name="job_title"]').setAttribute('value', this.dataset.jobtitle);
            document.querySelector('select[name="department"]').value = this.dataset.jobdepartment;
            document.querySelector('textarea[name="job_description"]').value = this.dataset.jobdescription;
            document.querySelector('select[name="status"]').value = this.dataset.jobstatus;
        });
    });
</script>