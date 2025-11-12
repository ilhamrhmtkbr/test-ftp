<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <!-- Update Name Section -->
    <div class="row mt-4">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card shadow mb-4" id="update-name">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Name</h6>
                </div>
                <div class="card-body">
                    <form action="/user/update-name" method="POST">
                        <?php
                        $badgeName = 'success-update-name';
                        require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                        $badgeName = 'error-update-name';
                        require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                        $inputType = 'text';
                        $name = 'name';
                        $value[$name] = $user->name ?? null;
                        require __DIR__ . '/../../Components/_form/InputText.php';
                        ?>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Email Section -->
    <div class="row mt-4">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card shadow mb-4" id="update-email">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Email</h6>
                </div>
                <div class="card-body">
                    <form action="/user/update-email" method="POST">
                        <?php
                        $badgeName = 'success-update-email';
                        require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                        $badgeName = 'error-update-email';
                        require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                        $inputType = 'email';
                        $name = 'email';
                        $value[$name] = $user->email ?? null;
                        require __DIR__ . '/../../Components/_form/InputText.php';
                        ?>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Password Section -->
    <div class="row mt-4">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card shadow mb-4" id="update-password">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Password</h6>
                </div>
                <div class="card-body">
                    <form action="/user/update-password" method="POST">
                        <?php
                        $badgeName = 'success-update-password';
                        require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                        $badgeName = 'error-update-password';
                        require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                        $inputType = 'password';
                        $name = 'oldPassword';
                        require __DIR__ . '/../../Components/_form/InputText.php';

                        $name = 'newPassword';
                        require __DIR__ . '/../../Components/_form/InputText.php';
                        ?>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Advance Personal Section -->
    <div class="row mt-4 mb-5">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card shadow mb-4" id="update-advance-personal">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Advance Personal</h6>
                </div>
                <div class="card-body">
                    <form action="/user/advance/personal" method="POST" enctype="multipart/form-data">
                        <?php
                        $badgeName = 'success-update-advance-personal';
                        require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                        $badgeName = 'error-update-advance-personal';
                        require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';

                        $name = 'image';
                        if ($data != null) {
                            $urlImage = "/assets/img/$data->image";
                        }
                        require __DIR__ . '/../../Components/_form/InputImage.php';

                        $inputType = 'tel';
                        $name = 'phone';
                        if ($data != null) {
                            $value[$name] = $data->phone;
                        }
                        require __DIR__ . '/../../Components/_form/InputText.php';

                        $inputType = 'text';
                        $name = 'headline';
                        if ($data != null) {
                            $value[$name] = $data->headline;
                        }
                        require __DIR__ . '/../../Components/_form/InputText.php';

                        $name = 'location';
                        if ($data != null) {
                            $value[$name] = $data->location;
                        }
                        require __DIR__ . '/../../Components/_form/InputText.php';
                        ?>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>