<?php

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
                    $searchAction = '/user/profile/portfolio';
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
        <div class="card-body">
            <?php if (count($data['results']) > 0) : ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <?php foreach ($data['results'] as $items) : ?>
                        <div class="card">
                            <img src="/assets/img/<?= $items->picture ?>" class="card-img-top" alt="<?= $items->title ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= $items->title ?></h5>
                                <p class="card-text"><?= $items->description ?></p>
                                <small class="card-subtitle text-muted d-block mb-3"><?= $items->link ?></small>

                                <div class="d-flex" style="gap: .5rem">
                                    <a class="btn btn-sm btn-outline-primary" href="#form-update"
                                       id="button-edit"
                                       data-edit_id="<?= $items->id ?>"
                                       data-title="<?= $items->title ?>"
                                       data-description="<?= $items->description ?>"
                                       data-link="<?= $items->link ?>"
                                       data-picture="<?= $items->picture ?>">
                                        Edit
                                    </a>
                                    <form action="/user/profile/portfolio" method="POST" class="m-0">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="id" value="<?= $items->id ?>">
                                        <input type="hidden" name="image" value="<?= $items->picture ?>">
                                        <button onclick="return confirm('Apakah kamu yakin ingin menghapus item ini?')"
                                                type="submit" class="btn btn-sm btn-outline-primary">
                                            Delete
                                        </button>
                                    </form>
                                </div>

                                <p class="card-subtitle text-muted mt-3 mb-0">
                                    <small><?= TimeHelper::getTimeBasic($items->created_at) ?></small>
                                </p>
                            </div>
                        </div>
                    <?php endforeach ?>
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
        <div class="card-body">
            <form action="/user/profile/portfolio" method="POST"
                  enctype="multipart/form-data"
                  accept="image/*">
                <h5><span>Add</span> Portfolio</h5>
                <input type="hidden" name="id" id="edit_id" value="">
                <?php

                $badgeName = 'success-update-profile-portfolio';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $badgeName = 'error-update-profile-portfolio';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $inputType = 'text';

                $name = 'picture';
                require __DIR__ . '/../../Components/_form/InputImage.php';

                $name = 'title';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'description';
                require __DIR__ . '/../../Components/_form/InputText.php';

                $name = 'link';
                require __DIR__ . '/../../Components/_form/InputText.php';
                ?>
                <br>
                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script>
    const buttonEdit = document.querySelectorAll('#button-edit');
    buttonEdit.forEach((button) => {
        button.addEventListener('click', function () {
            document.getElementsByTagName('span')[0].innerHTML = 'Update';
            document.querySelector('#imageOld').src = '/assets/img/' + this.dataset.picture;
            document.querySelector('#edit_id').value = this.dataset.edit_id;
            document.querySelector('input[name="title"]').value = this.dataset.title;
            document.querySelector('input[name="description"]').value = this.dataset.description;
            document.querySelector('input[name="link"]').value = this.dataset.link;
        });
    });
</script>