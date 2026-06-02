<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";
require_once __DIR__ . "/../../core/CSRF.php";

Auth::requireRole("doctor");

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Add Prescription</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        Prescription for Appointment #<?= sanitize($appointment["id"]) ?>
                    </h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>index.php?page=prescriptions&action=store" enctype="multipart/form-data">
                    <div class="card-body">

                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="appointment_id" value="<?= sanitize($appointment["id"]) ?>">

                        <div class="form-group">
                            <label>Patient</label>
                            <input type="text" class="form-control" value="<?= sanitize($appointment["patient_name"]) ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Appointment Date</label>
                            <input type="text" class="form-control" value="<?= sanitize($appointment["appt_date"]) ?> <?= sanitize(formatTime($appointment["appt_time"])) ?>" disabled>
                        </div>

                        <div class="form-group">
                            <label>Diagnosis</label>
                            <textarea name="diagnosis" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Medications</label>
                            <textarea name="medications" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Prescription PDF Optional</label>
                            <input type="file" name="prescription_file" class="form-control" accept="application/pdf">
                            <small class="text-muted">
                                PDF only, max 3 MB.
                            </small>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Save Prescription
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