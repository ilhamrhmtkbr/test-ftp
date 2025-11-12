<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>
<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Project Assignments</h6>
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
                    $searchAction = '/hr/employee/project-assignmentss';
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

                    $listName = 'Roles';
                    $listParam = 'role';
                    $listItem = array_values($data['company_employee_projects']);
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
            <?php if (count($data['employee_project_assignments']['results']) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No. </th>
                            <?php foreach (array_keys((array)$data['employee_project_assignments']['results'][0]) as $key): ?>
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
                        foreach ($data['employee_project_assignments']['results'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <?php if ($key == 'id' || $key == 'email') {
                                        continue;
                                    } ?>
                                    <td <?= ($key == 'assigned_date') ? "style='text-align:center;'" : '' ?>>
                                        <?php if ($key == 'assigned_date') {
                                            echo TimeHelper::getTime($value);
                                        } else {
                                            echo $value;
                                        } ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap: .5rem">
                                    <a href="/hr/employee/details?id=<?= $items['email'] ?>" class="btn btn-sm btn-primary">Detail</a>
                                    <a href="#form-update-employee-project-assignment" class="btn btn-sm btn-primary"
                                       id="button-edit"
                                       data-project_assignment_id="<?= $items['id'] ?>"
                                       data-email="<?= $items['email'] ?>"
                                       data-project_id="<?= array_search($items['project_name'], $data['company_employee_projects']) ?>"
                                       data-role_in_project="<?= $items['role_in_project'] ?>"
                                       data-assigned_date="<?= $items['assigned_date'] ?>">Edit</a>
                                    <form action="/hr/employee/project-assignment" method="POST">
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
                $data['total-page'] = $data['employee_project_assignments']['total-page'];
                require __DIR__ . '/../../Components/Pagination.php'
                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>
        </div>
    </div>

    <?php
    $floatName = 'success-delete-employee-project-assignment';
    require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

    $floatName = 'error-delete-employee-project-assignment';
    require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';
    ?>

    <br>
    <br id="form-update-employee-project-assignment">
    <br>
    <br>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <span id="form-title">Add</span> Employee
            </h6>
        </div>
        <div class="card-body">
            <form action="/hr/employee/project-assignment" method="POST">
                <input type="hidden" name="id" id="editId" value="">
                <?php

                $floatName = 'success-update-employee-project-assignment';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-employee-project-assignment';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'email';
                $inputType = 'email';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'project_id';
                $options = $data['company_employee_projects'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                $name = 'role_in_project';
                $inputType = 'text';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'assigned_date';
                $inputType = 'date';
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
            document.querySelector('#editId').setAttribute('value', this.dataset.project_assignment_id);
            document.querySelector('input[name="email"]').setAttribute('value', this.dataset.email);
            document.querySelector('input[name="email"]').readOnly = true;
            document.querySelector('select[name="project_id"]').value = this.dataset.project_id;
            document.querySelector('input[name="role_in_project"]').value = this.dataset.role_in_project;
            document.querySelector('input[name="assigned_date"]').setAttribute('value', this.dataset.assigned_date);
        });
    });
</script>