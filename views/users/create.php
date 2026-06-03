<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";
require_once __DIR__ . "/../../core/CSRF.php";

Auth::requireRole("admin");

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create User</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=users">Users</a>
                        </li>
                        <li class="breadcrumb-item active">Create User</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">New User Account</h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>index.php?page=users&action=create">
                    <div class="card-body">

                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Temporary Password</label>
                            <input type="password" name="password" class="form-control" required>
                            <small class="text-muted">Minimum 8 characters.</small>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                                <option value="patient">Patient</option>
                                <option value="doctor">Doctor</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <hr>

                        <h5>Doctor Details</h5>
                        <p class="text-muted">
                            Fill this section only if the selected role is Doctor.
                        </p>

                        <div class="form-group">
                            <label>Specialization</label>
                            <select name="specialization_id" class="form-control">
                                <option value="">Select specialization</option>

                                <?php foreach ($specializations as $specialization): ?>
                                    <option value="<?= sanitize($specialization["id"]) ?>">
                                        <?= sanitize($specialization["name"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Consultation Fee</label>
                            <input type="number" step="0.01" name="consultation_fee" class="form-control" value="0.00">
                        </div>

                        <div class="form-group">
                            <label>Available Days</label>

                            <div class="row">
                                <?php
                                $days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
                                ?>

                                <?php foreach ($days as $day): ?>
                                    <div class="col-md-2">
                                        <label>
                                            <input type="checkbox" name="available_days[]" value="<?= $day ?>">
                                            <?= $day ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Create User
                        </button>

                        <a href="<?= BASE_URL ?>index.php?page=users" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>