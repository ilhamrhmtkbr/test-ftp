<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;

?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="/assets/img/<?= $data['user_advance_personal'][0]['personal_image'] ?>" class="img-profile rounded-circle mb-3" width="150">
                    <h5><?= $user->name ?></h5>
                    <p class="text-muted"><?= $roleName ?></p>
                    <a href="/user/advance/personal" class="btn btn-outline-primary">Edit</a>
                </div>
                <div class="col-md-8">
                    <form>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" class="form-control" value="<?= $user->name ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" value="<?= $user->email ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" value="<?= $user->email ?>" readonly>
                        </div>
                    </form>
                    <?php if ($data['user_advance_personal']) : ?>
                        <form>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" class="form-control"
                                       value="<?= $data['user_advance_personal'][0]['personal_phone'] ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Headline</label>
                                <input type="text" class="form-control"
                                       value="<?= $data['user_advance_personal'][0]['personal_headline'] ?>"
                                       readonly>
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" class="form-control"
                                       value="<?= $data['user_advance_personal'][0]['personal_location'] ?>"
                                       readonly>
                            </div>
                        </form>
                    <?php else : ?>
                        <p class="text-danger"> Anda belum mengatur data </p>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h5 class="card-title mb-0">Skills</h5>
                </div>
                <div class="col-auto">
                    <a href="/user/advance/skill" class="btn btn-outline-primary">Edit</a>
                </div>
            </div>
            <?php if ($data['user_advance_skills']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['user_advance_skills'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['user_advance_skills'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td <?= $key == 'skill_rating' ? "class='text-center'" : '' ?>><?= $value ?></td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-danger"> Anda belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h5 class="card-title mb-0">Socials</h5>
                </div>
                <div class="col-auto">
                    <a href="/user/advance/social" class="btn btn-outline-primary">Edit</a>
                </div>
            </div>
            <?php if ($data['user_advance_socials']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['user_advance_socials'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['user_advance_socials'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td <?= $key == 'app_name' || $key == 'created_at' ? "class='text-center'" : '' ?>><?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?></td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-danger"> Anda belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h5 class="card-title mb-0">Education</h5>
                </div>
                <div class="col-auto">
                    <a href="/user/profile/education" class="btn btn-outline-primary">Edit</a>
                </div>
            </div>
            <?php if ($data['user_profile_education']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['user_profile_education'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['user_profile_education'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td <?= $key == 'graduation_year' ? "class='text-center'" : '' ?>>
                                        <?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-danger"> Anda belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h5 class="card-title mb-0">Experience</h5>
                </div>
                <div class="col-auto">
                    <a href="/user/profile/experience" class="btn btn-outline-primary">Edit</a>
                </div>
            </div>
            <?php if ($data['user_profile_experience']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['user_profile_experience'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['user_profile_experience'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td <?= $key == 'work_duration' ? "class='text-center'" : '' ?>>
                                        <?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-danger"> Anda belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h5 class="card-title mb-0">Portfolio</h5>
                </div>
                <div class="col-auto">
                    <a href="/user/profile/portfolio" class="btn btn-outline-primary">Edit</a>
                </div>
            </div>
            <?php if ($data['user_profile_portfolio']) : ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <?php foreach (array_keys($data['user_profile_portfolio'][0]) as $name): ?>
                                <th><?= StringHelper::toCapitalize($name) ?></th>
                            <?php endforeach ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $counter = 1;
                        foreach ($data['user_profile_portfolio'] as $items) : ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <?php foreach ($items as $key => $value) : ?>
                                    <td><?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?></td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="text-danger"> Anda belum mengatur data </p>
            <?php endif ?>
        </div>
    </div>
</div>