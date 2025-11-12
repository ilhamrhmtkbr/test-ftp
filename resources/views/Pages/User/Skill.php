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
                    $searchAction = '/user/advance/skill';
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
                                    <td <?= $key == 'rating' ? "style='text-align:center;'" : '' ?>
                                            <?= $key == 'description' ? "class='fit'" : '' ?> />
                                    <?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?>
                                    </td>
                                <?php endforeach ?>
                                <td class="d-flex" style="gap: 0.5rem">
                                    <a class="btn btn-primary btn-sm" href="#form-update"
                                       id="button-edit"
                                       data-edit_id="<?= $items->id ?>"
                                       data-name="<?= $items->name ?>"
                                       data-rating="<?= $items->rating ?>"
                                       data-description="<?= $items->description ?>">Edit</a>
                                    <form action="/user/advance/skill" method="POST">
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
            <h6 class="text-primary font-weight-bold m-0"><span>Add</span> Skill</h6>
        </div>
        <div class="card-body">
            <form action="/user/advance/skill" method="POST">
                <input type="hidden" name="id" id="edit_id" value="">
                <?php

                $badgeName = 'success-update-advance-skill';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $badgeName = 'error-update-advance-skill';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $inputType = 'text';

                $name = 'name';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'rating';
                $options = ['1', '2', '3', '4', '5'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                $name = 'description';
                require __DIR__ . '/../../Components/_form/InputText.php';

                ?>
                <br>
                <div class="d-flex" style="gap: 0.5rem" onclick="location.reload()">
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
            document.getElementsByTagName('span')[0].innerHTML = 'Update';
            document.querySelector('#edit_id').value = this.dataset.edit_id;
            document.querySelector('input[name="name"]').value = this.dataset.name;
            document.querySelector('select[name="rating"]').value = this.dataset.rating;
            document.querySelector('input[name="description"]').value = this.dataset.description;
        });
    });
</script>