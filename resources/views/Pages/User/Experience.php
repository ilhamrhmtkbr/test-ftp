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
                    $searchAction = '/user/profile/experience';
                    $searchPlaceholder = 'Title and Description...';
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
            Data
        </div>
        <div class="card-body">
            <?php if (count($data['results']) > 0) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys((array)$data['results'][0]) as $key): ?>
                                <?php if ($key == 'id' || $key == 'user_id') {
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
                                    <?php if ($key == 'id' || $key == 'user_id') {
                                        continue;
                                    } ?>
                                    <td <?= $key == 'work_duration' ? "style='text-align:center;'" : '' ?>
                                            <?= $key == 'job_description' ? "class='fit'" : '' ?> />
                                    <?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap: .5rem">
                                    <a class="btn btn-primary btn-sm" href="#form-update"
                                       id="button-edit"
                                       data-edit_id="<?= $items->id ?>"
                                       data-job_title="<?= $items->job_title ?>"
                                       data-job_description="<?= $items->job_description ?>"
                                       data-company_name="<?= $items->company_name ?>"
                                       data-work_duration="<?= $items->work_duration ?>">Edit</a>
                                    <form action="/user/profile/experience" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $items->id ?>">
                                        <button onclick="return confirm('Apakah kamu yakin ingin menghapus item ini?')"
                                                type="submit"
                                                class="btn btn-primary btn-sm">Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                <?php
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
            <h6 class="m-0 font-weight-bold text-primary"><span>Add</span> Experience</h6>
        </div>
        <div class="card-body">
            <form action="/user/profile/experience" method="POST">
                <input type="hidden" name="id" id="edit_id" value="">
                <?php

                $badgeName = 'success-update-profile-experience';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $badgeName = 'error-update-profile-experience';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $inputType = 'text';

                $name = 'job_title';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'job_description';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'company_name';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'work_duration';
                $options = range(1, 10);
                $additionalInfo = 'Durasi dalam tahun';
                require __DIR__ . '/../../Components/_form/InputSelect.php';
                ?>
                <div style="gap: .5rem" class="d-flex" onclick="location.reload()">
                    <div class="btn btn-sm btn-outline-primary">Cancel</div>
                    <button type="submit" class="bt btn-sm btn-primary">Submit</button>
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
            document.querySelector('#edit_id').value = this.dataset.edit_id;
            document.querySelector('input[name="job_title"]').value = this.dataset.job_title;
            document.querySelector('input[name="job_description"]').value = this.dataset.job_description;
            document.querySelector('input[name="company_name"]').value = this.dataset.company_name;
            document.querySelector('select[name="work_duration"]').value = this.dataset.work_duration;
        });
    });
</script>