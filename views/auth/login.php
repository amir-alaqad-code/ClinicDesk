<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../core/CSRF.php";
require_once __DIR__ . "/../../core/helpers.php";

$pageTitle = "Login";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <!-- Responsive layout for all devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Page title -->
    <title><?= APP_NAME ?> | Login</title>

    <!-- Font Awesome icons from local AdminLTE files -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">

    <!-- AdminLTE main CSS from local files -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">

    <div class="login-box">

        <div class="login-logo">
            <a href="#">
                <b>Clinic</b>Desk
            </a>
        </div>

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h4 class="mb-0">Clinic Management Login</h4>
            </div>

            <div class="card-body">

                <p class="login-box-msg">
                    Sign in to access your dashboard
                </p>

                <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

                <form method="POST" action="<?= BASE_URL ?>index.php?page=auth&action=login">

                    <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                    <div class="input-group mb-3">
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Email"
                            required>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="Password"
                            required>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        Login
                    </button>

                </form>

                <hr>

                <div class="text-center text-muted small">
                    <p class="mb-1">
                        This system was developed by
                    </p>
                    <p class="mb-0 font-weight-bold text-primary">
                        Dev. Amir Alaqad
                    </p>
                </div>

            </div>
        </div>

    </div>

    <!-- jQuery from local AdminLTE files -->
    <script src="<?= BASE_URL ?>public/assets/adminlte/plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap JS from local AdminLTE files -->
    <script src="<?= BASE_URL ?>public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE main JS -->
    <script src="<?= BASE_URL ?>public/assets/adminlte/dist/js/adminlte.min.js"></script>

</body>

</html>