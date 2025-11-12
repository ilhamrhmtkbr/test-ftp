<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;

if (!isset($_GET['id'])) {
    header('Location: ' . '/hr/candidates');
    exit;
}
?>

<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="/assets/img/<?= isset($data['user_personal']) ? $data['user_personal'][0]['image'] : '' ?>"
                         alt="Photo"
                         class="img-fluid rounded shadow"
                    >
                </div>
                <div class="col-md-8">
                    <form>
                        <?php if (isset($data['user_personal'][0]) && $data['user_personal'][0]) : ?>
                            <div class="form-group">
                                <label>Name</label>
                                <input class="form-control" value="<?= $data['user_personal'][0]['name'] ?>" readonly/>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" value="<?= $data['user_personal'][0]['email'] ?>" readonly/>
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input class="form-control" value="<?= $data['user_personal'][0]['phone'] ?>" readonly/>
                            </div>
                            <div class="form-group">
                                <label>Headline</label>
                                <input class="form-control" value="<?= $data['user_personal'][0]['headline'] ?>"
                                       readonly/>
                            </div>
                            <div class="form-group">
                                <label>Location</label>
                                <input class="form-control" value="<?= $data['user_personal'][0]['location'] ?>"
                                       readonly/>
                            </div>
                        <?php else : ?>
                            <p class="text-danger text-center mt-4" style="align-self: center;"> Candidate belum
                                mengatur data </p>
                        <?php endif ?>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <p>Skills</p>
                    <?php if ($data['user_skills']) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <?php foreach (array_keys($data['user_skills'][0]) as $name): ?>
                                        <th><?= StringHelper::toCapitalize($name) ?></th>
                                    <?php endforeach ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $counter = 1;
                                foreach ($data['user_skills'] as $items) : ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <?php foreach ($items as $key => $value) : ?>
                                            <td <?= $key == 'skill_rating' ? "style='text-align:center;'" : '' ?>><?= $value ?></td>
                                        <?php endforeach ?>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-danger text-center mt-4" style="align-self: center;"> Candidate belum mengatur
                            data </p>
                    <?php endif ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <p>Socials</p>
                    <?php if ($data['user_socials']) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <?php foreach (array_keys($data['user_socials'][0]) as $name): ?>
                                        <th><?= StringHelper::toCapitalize($name) ?></th>
                                    <?php endforeach ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $counter = 1;
                                foreach ($data['user_socials'] as $items) : ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <?php foreach ($items as $key => $value) : ?>
                                            <td <?= $key == 'app_name' || $key == 'created_at' ? "style='text-align:center;'" : '' ?>><?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?></td>
                                        <?php endforeach ?>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-danger text-center mt-4" style="align-self: center;"> Candidate belum mengatur
                            data </p>
                    <?php endif ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <p>Experience</p>

                    <?php if ($data['user_experience']) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <?php foreach (array_keys($data['user_experience'][0]) as $name): ?>
                                        <th><?= StringHelper::toCapitalize($name) ?></th>
                                    <?php endforeach ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $counter = 1;
                                foreach ($data['user_experience'] as $items) : ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <?php foreach ($items as $key => $value) : ?>
                                            <td <?= $key == 'work_duration' ? "style='text-align:center;'" : '' ?>>
                                                <?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?>
                                            </td>
                                        <?php endforeach ?>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-danger text-center mt-4" style="align-self: center;"> Candidate belum mengatur
                            data </p>
                    <?php endif ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <p>Portfolio</p>

                    <?php if ($data['user_portfolio']) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <?php foreach (array_keys($data['user_portfolio'][0]) as $name): ?>
                                        <th><?= StringHelper::toCapitalize($name) ?></th>
                                    <?php endforeach ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $counter = 1;
                                foreach ($data['user_portfolio'] as $items) : ?>
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
                        <p class="text-danger text-center mt-4" style="align-self: center;"> Candidate belum mengatur
                            data </p>
                    <?php endif ?>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <p>Education</p>

                    <?php if ($data['user_education']) : ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <?php foreach (array_keys($data['user_education'][0]) as $name): ?>
                                        <th><?= StringHelper::toCapitalize($name) ?></th>
                                    <?php endforeach ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $counter = 1;
                                foreach ($data['user_education'] as $items) : ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <?php foreach ($items as $key => $value) : ?>
                                            <td <?= $key == 'graduation_year' ? "style='text-align:center;'" : '' ?>>
                                                <?= $key == 'created_at' ? TimeHelper::getTimeBasic($value) : $value ?>
                                            </td>
                                        <?php endforeach ?>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <p class="text-danger text-center mt-4" style="align-self: center;"> Candidate belum mengatur
                            data </p>
                    <?php endif ?>
                </div>

            </div>
        </div>
    </div>

    <br>
    <br id="form-send-email">
    <br>
    <br>

    <div class="card shadow">
        <div class="card-body">
            <form action="/hr/candidate/interview" method="POST">
                <p class="gen-form-title">Send Email for Interview</p>
                <input type="hidden" name="email" value="<?= $_GET['id'] ?>">
                <?php

                $badgeName = 'success-send-email';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $badgeName = 'error-send-email';
                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                $name = 'message';
                require __DIR__ . '/../../Components/_form/InputTextarea.php';

                ?>
                <div class="d-flex" style="gap: .5rem"
                     onclick="location.reload()">
                    <div class="btn btn-sm btn-outline-primary">Cancel</div>
                    <button style="place-self:center" type="submit" class="btn btn-sm btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <br>
    <br id="form-update-status">
    <br>
    <br>

    <div class="card shadow">
        <div class="card-body">
            <form action="/hr/candidate/update" method="POST">
                <p class="gen-form-title">Send Email for Interview</p>
                <input type="hidden" name="email" value="<?= $_GET['id'] ?>">
                <?php

                $floatName = 'success-update-status';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $floatName = 'error-update-status';
                require __DIR__ . '/../../Components/_alert/FloatMessageWithClose.php';

                $name = 'status';
                $options = ['applied', 'interviewed', 'hired', 'rejected'];
                require __DIR__ . '/../../Components/_form/InputSelect.php';

                ?>
                <div class="d-flex" style="gap: .5rem"
                     onclick="location.reload()">
                    <div class="btn btn-sm btn-outline-primary">Cancel</div>
                    <button style="place-self:center" type="submit" class="btn btn-sm btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>