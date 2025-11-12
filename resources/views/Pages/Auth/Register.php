<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/fontawesome-all.min.css"/>
    <link rel="stylesheet" href="/assets/css/sb-admin-2.min.css"/>
    <link
            href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
            rel="stylesheet">
    <title></title>
</head>

<body class="bg-gradient-primary">

<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <img class="col-lg-6 d-none d-lg-block" src="/assets/img/talenthub.png">
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Register</h1>
                                </div>
                                <?php

                                if (isset($errorData)) echo $errorData;
                                if (isset($sessionFlash)) echo $sessionFlash;

                                $badgeName = 'error-register';
                                require __DIR__ . '/../../Components/_alert/BadgeWithClose.php';
                                ?>
                                <form action="/user/register" method="POST" class="user">
                                    <div class="form-group">
                                        <input type="email" name="email" value="<?= $_POST['email'] ?? '' ?>"
                                               class="form-control form-control-user"
                                               id="inputEmail" aria-describedby="emailHelp"
                                               placeholder="Enter Email Address...">
                                        <?php if (isset($session['errors']) && isset($session['errors']['email'])) : ?>
                                            <div class=" text-danger"><?= $session['errors']['email'][0] ?></div>
                                        <?php endif ?>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" value="<?= $_POST['password'] ?? '' ?>" name="password"
                                               class="form-control form-control-user"
                                               id="inputPassword" placeholder="Password">
                                        <?php if (isset($session['errors']) && isset($session['errors']['password'])) : ?>
                                            <div class=" text-danger"><?= $session['errors']['password'][0] ?></div>
                                        <?php endif ?>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" value="<?= $_POST['password'] ?? '' ?>" name="passwordConfirm"
                                               class="form-control form-control-user"
                                               id="inputPasswordConfirm" placeholder="Password Confirm">
                                        <?php if (isset($session['errors']) && isset($session['errors']['passwordConfirm'])) : ?>
                                            <div class=" text-danger"><?= $session['errors']['passwordConfirm'][0] ?></div>
                                        <?php endif ?>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Register
                                    </button>
                                </form>
                                <div class="text-center">
                                    <a class="small" href="/user/login">Already Have An Account!</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Bootstrap core JavaScript-->
<script src="/assets/js/sb-admin-2/jquery.min.js"></script>
<script src="/assets/js/sb-admin-2/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="/assets/js/sb-admin-2/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="/assets/js/sb-admin-2/sb-admin-2.min.js"></script>
</body>

</html>