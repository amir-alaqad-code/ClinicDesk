<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";

Auth::requireRole("doctor");

$pageTitle = "Doctor Dashboard";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Doctor Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= sanitize($totalThisMonth) ?></h3>
                            <p>This Month Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= sanitize($pendingCount) ?></h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= sanitize($completedCount) ?></h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Upcoming Appointments</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($upcomingAppointments)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No upcoming appointments.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($upcomingAppointments as $appointment): ?>
                                <tr>
                                    <td><?= sanitize($appointment["patient_name"]) ?></td>
                                    <td><?= sanitize($appointment["appt_date"]) ?></td>
                                    <td><?= sanitize(formatTime($appointment["appt_time"])) ?></td>
                                    <td><?= sanitize($appointment["status"]) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>