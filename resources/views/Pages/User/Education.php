<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;

?>

<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Education</h6>
            <div class="d-flex" style="gap: .5rem">
                <a href="<?= UrlHelper::getPathInfo() ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-redo"></i> Reset Filter
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <?php
                    $searchAction = '/user/profile/education';
                    $searchPlaceholder = 'Institution...';
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
            <?php if (count($data['education']['results']) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No. </th>
                            <?php foreach (array_keys((array)$data['education']['results'][0]) as $key): ?>
                                <?php if ($key == 'id' || $key == 'user_id'  || $key == 'degree_id') {
                                    continue;
                                } ?>
                                <th><?= StringHelper::toCapitalize($key) ?></th>
                            <?php endforeach ?>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['education']['results'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <?php if ($key == 'id' || $key == 'user_id' || $key == 'degree_id') {
                                        continue;
                                    } ?>
                                    <td <?= $key == 'graduation_year' ? "style='text-align:center;'" : '' ?>
                                            <?= $key == 'institution' ? "class='fit'" : '' ?> />
                                    <?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap: .5rem">
                                    <a class="btn btn-primary btn-sm" href="#form-update"
                                       id="button-edit"
                                       data-edit_id="<?= $items->id ?>"
                                       data-institution="<?= $items->institution ?>"
                                       data-degree_id="<?= $items->degree_id ?>"
                                       data-field="<?= $items->field ?>"
                                       data-graduation_year="<?= $items->graduation_year ?>">Edit</a>
                                    <form action="/user/profile/education" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $items->id ?>">
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
                $data['total-page'] = $data['education']['total-page'];
                require __DIR__ . '/../../Components/Pagination.php'
                ?>
            <?php else : ?>
                <p class="text-danger text-center mt-4">No result</p>
            <?php endif ?>
        </div>
    </div>

    <br>
    <br id="form-update">
    <br>
    <br>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><span>Add</span> Education</h6>
        </div>
        <div class="card-body">
            <form action="/user/profile/education" method="POST">
                <input type="hidden" name="id" id="edit_id" value="">
                <?php

                $badgeName = 'success-update-profile-education';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $badgeName = 'error-update-profile-education';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $inputType = 'text';

                $name = 'institution';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'degree_id';
                $options = [];
                foreach ($data['education-degree'] as $educationDegree) {
                    $options[$educationDegree->id] = $educationDegree->degree;
                }
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                $name = 'field';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'graduation_year';
                $options = range(1990, 2024);
                require __DIR__ . '/../../Components/_form/InputSelect.php';
                ?>
                <div class="d-flex" style="gap: .5rem" onclick="location.reload()">
                    <div class="btn btn-outline-primary btn-sm">Cancel</div>
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
            document.getElementsByTagName('span')[0].innerHTML = 'Update';
            document.querySelector('#edit_id').value = this.dataset.edit_id;
            document.querySelector('input[name="institution"]').value = this.dataset.institution;
            document.querySelector('select[name="degree_id"]').value = this.dataset.degree_id;
            document.querySelector('input[name="field"]').value = this.dataset.field;
            document.querySelector('select[name="graduation_year"]').value = this.dataset.graduation_year;
        });
    });
</script>