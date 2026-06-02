<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";

Auth::requireRole("admin");

$pageTitle = "Admin Dashboard";

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$roleTotals = [
    "admin" => 0,
    "doctor" => 0,
    "patient" => 0
];

foreach ($usersByRole as $row) {
    $roleTotals[$row["role"]] = (int) $row["total"];
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Admin Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= sanitize($roleTotals["admin"]) ?></h3>
                            <p>Admins</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= sanitize($roleTotals["doctor"]) ?></h3>
                            <p>Doctors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= sanitize($roleTotals["patient"]) ?></h3>
                            <p>Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?= sanitize($appointmentsToday) ?></h3>
                            <p>Appointments Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">This Week by Status</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($weekStatusCounts)): ?>
                        <p class="text-muted">No appointments this week.</p>
                    <?php endif; ?>

                    <?php foreach ($weekStatusCounts as $row): ?>
                        <span class="badge badge-secondary mr-2">
                            <?= sanitize($row["status"]) ?>: <?= sanitize($row["total"]) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Recent 5 Appointments</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($recentAppointments)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No recent appointments.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($recentAppointments as $appointment): ?>
                                <tr>
                                    <td><?= sanitize($appointment["patient_name"]) ?></td>
                                    <td><?= sanitize($appointment["doctor_name"]) ?></td>
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