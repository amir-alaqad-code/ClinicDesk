<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";

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
                    <h1>Reports</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Reports</li>
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
                    <h3 class="card-title mb-0">Appointment Report</h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>index.php" class="mb-4">
                        <input type="hidden" name="page" value="reports">

                        <div class="row">
                            <div class="col-md-3">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?= sanitize($_GET["start_date"] ?? "") ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?= sanitize($_GET["end_date"] ?? "") ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label>Doctor</label>
                                <select name="doctor_id" class="form-control">
                                    <option value="">All Doctors</option>

                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= sanitize($doctor["id"]) ?>" <?= (($_GET["doctor_id"] ?? "") == $doctor["id"]) ? "selected" : "" ?>>
                                            <?= sanitize($doctor["name"]) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <?php foreach (["pending", "confirmed", "completed", "cancelled"] as $status): ?>
                                        <option value="<?= $status ?>" <?= (($_GET["status"] ?? "") === $status) ? "selected" : "" ?>>
                                            <?= ucfirst($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                Generate Report
                            </button>

                            <?php if (!empty($reportRows)): ?>
                                <button type="submit" name="export" value="csv" class="btn btn-success">
                                    Export CSV
                                </button>
                            <?php endif; ?>

                            <a href="<?= BASE_URL ?>index.php?page=reports" class="btn btn-secondary">
                                Reset
                            </a>
                        </div>
                    </form>

                    <?php if (!empty($reportRows)): ?>
                        <div class="alert alert-info">
                            Total shown: <?= sanitize(count($reportRows)) ?>

                            <?php foreach ($statusCounts as $status => $count): ?>
                                <span class="badge badge-secondary ml-2">
                                    <?= sanitize($status) ?>: <?= sanitize($count) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($reportRows)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            No report data. Select a date range to generate a report.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($reportRows as $row): ?>
                                    <tr>
                                        <td><?= sanitize($row["patient_name"]) ?></td>
                                        <td><?= sanitize($row["doctor_name"]) ?></td>
                                        <td><?= sanitize($row["specialization_name"]) ?></td>
                                        <td><?= sanitize($row["appt_date"]) ?></td>
                                        <td><?= sanitize(formatTime($row["appt_time"])) ?></td>
                                        <td><?= sanitize($row["status"]) ?></td>
                                        <td><?= sanitize($row["reason"] ?? "-") ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>