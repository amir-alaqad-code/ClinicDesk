<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";
require_once __DIR__ . "/../../core/CSRF.php";

Auth::requireRole("patient");

$timeSlots = [
    "09:00:00",
    "09:30:00",
    "10:00:00",
    "10:30:00",
    "11:00:00",
    "11:30:00",
    "12:00:00",
    "12:30:00",
    "13:00:00",
    "13:30:00",
    "14:00:00",
    "14:30:00",
    "15:00:00",
    "15:30:00",
    "16:00:00"
];

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Book Appointment</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=appointments">Appointments</a>
                        </li>
                        <li class="breadcrumb-item active">Book Appointment</li>
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
                    <h3 class="card-title mb-0">New Appointment</h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>index.php?page=appointments&action=book">
                    <div class="card-body">

                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="form-group">
                            <label>Doctor</label>
                            <select name="doctor_id" class="form-control" required>
                                <option value="">Select doctor</option>

                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= sanitize($doctor["id"]) ?>">
                                        <?= sanitize($doctor["name"]) ?>
                                        -
                                        <?= sanitize($doctor["specialization_name"]) ?>
                                        |
                                        Available:
                                        <?= sanitize($doctor["available_days"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <small class="text-muted">
                                Choose a date that matches the doctor's available days.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Preferred Date</label>
                            <input type="date" name="appt_date" class="form-control" min="<?= date("Y-m-d") ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Time Slot</label>
                            <select name="appt_time" class="form-control" required>
                                <option value="">Select time</option>

                                <?php foreach ($timeSlots as $slot): ?>
                                    <option value="<?= $slot ?>">
                                        <?= date("H:i", strtotime($slot)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" name="reason" class="form-control" maxlength="255" placeholder="Short reason for the appointment">
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Book Appointment
                        </button>

                        <a href="<?= BASE_URL ?>index.php?page=appointments" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>