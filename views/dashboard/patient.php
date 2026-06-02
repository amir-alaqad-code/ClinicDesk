<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";

Auth::requireRole("patient");

$pageTitle = "Patient Dashboard";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Patient Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= sanitize($activeAppointments) ?></h3>
                            <p>Active Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= sanitize($completedAppointments) ?></h3>
                            <p>Completed Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= sanitize($prescriptionsCount) ?></h3>
                            <p>Prescriptions</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Next Appointment</h3>
                </div>

                <div class="card-body">
                    <?php if (!$nextAppointment): ?>
                        <p class="text-muted">No upcoming appointment.</p>
                    <?php else: ?>
                        <p><strong>Doctor:</strong> <?= sanitize($nextAppointment["doctor_name"]) ?></p>
                        <p><strong>Date:</strong> <?= sanitize($nextAppointment["appt_date"]) ?></p>
                        <p><strong>Time:</strong> <?= sanitize(formatTime($nextAppointment["appt_time"])) ?></p>
                        <p><strong>Status:</strong> <?= sanitize($nextAppointment["status"]) ?></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>