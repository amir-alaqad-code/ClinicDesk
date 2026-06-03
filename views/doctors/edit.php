<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";
require_once __DIR__ . "/../../core/CSRF.php";

Auth::requireRole("admin");

$selectedDays = explode(",", $doctor["available_days"] ?? "");
$days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Doctor</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=doctors">Doctors</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Doctor</li>
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
                    <h3 class="card-title mb-0">
                        Edit <?= sanitize($doctor["name"]) ?>
                    </h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>index.php?page=doctors&action=update">
                    <div class="card-body">

                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="doctor_id" value="<?= sanitize($doctor["id"]) ?>">

                        <div class="form-group">
                            <label>Doctor Name</label>
                            <input type="text" class="form-control" value="<?= sanitize($doctor["name"]) ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="<?= sanitize($doctor["email"]) ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Specialization</label>
                            <select name="specialization_id" class="form-control" required>
                                <?php foreach ($specializations as $specialization): ?>
                                    <option
                                        value="<?= sanitize($specialization["id"]) ?>"
                                        <?= $specialization["name"] === $doctor["specialization_name"] ? "selected" : "" ?>>
                                        <?= sanitize($specialization["name"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" class="form-control" rows="4"><?= sanitize($doctor["bio"] ?? "") ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Consultation Fee</label>
                            <input
                                type="number"
                                step="0.01"
                                name="consultation_fee"
                                class="form-control"
                                value="<?= sanitize($doctor["consultation_fee"]) ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label>

                            <div class="row">
                                <?php foreach ($days as $day): ?>
                                    <div class="col-md-2">
                                        <label>
                                            <input
                                                type="checkbox"
                                                name="available_days[]"
                                                value="<?= $day ?>"
                                                <?= in_array($day, $selectedDays) ? "checked" : "" ?>>
                                            <?= $day ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>

                        <a href="<?= BASE_URL ?>index.php?page=doctors" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>