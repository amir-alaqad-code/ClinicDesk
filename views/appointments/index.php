<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";
require_once __DIR__ . "/../../core/CSRF.php";

Auth::requireRole("admin", "doctor", "patient");

$role = Auth::role();
$csrfToken = CSRF::generateToken();

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Appointments</h1>

                <?php if ($role === "patient"): ?>
                    <a href="<?= BASE_URL ?>index.php?page=appointments&action=book" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Book Appointment
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Appointments List</h3>
                </div>

                <div class="card-body">

                    <form method="GET" action="<?= BASE_URL ?>index.php" class="mb-3">
                        <input type="hidden" name="page" value="appointments">

                        <div class="row">
                            <?php if ($role === "admin"): ?>
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
                                    <label>Patient Name</label>
                                    <input type="text" name="patient_name" class="form-control" value="<?= sanitize($_GET["patient_name"] ?? "") ?>">
                                </div>
                            <?php endif; ?>

                            <div class="col-md-2">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All</option>
                                    <?php foreach (["pending", "confirmed", "completed", "cancelled"] as $status): ?>
                                        <option value="<?= $status ?>" <?= (($_GET["status"] ?? "") === $status) ? "selected" : "" ?>>
                                            <?= ucfirst($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="<?= sanitize($_GET["start_date"] ?? "") ?>">
                            </div>

                            <div class="col-md-2">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="<?= sanitize($_GET["end_date"] ?? "") ?>">
                            </div>

                            <div class="col-md-2 d-flex align-items-end mt-2">
                                <button type="submit" class="btn btn-secondary btn-block">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>

                                    <?php if ($role !== "patient"): ?>
                                        <th>Patient</th>
                                    <?php endif; ?>

                                    <?php if ($role !== "doctor"): ?>
                                        <th>Doctor</th>
                                    <?php endif; ?>

                                    <th>Specialization</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th>Notes</th>
                                    <th width="220">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($appointments)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No appointments found.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?= sanitize($appointment["id"]) ?></td>

                                        <?php if ($role !== "patient"): ?>
                                            <td><?= sanitize($appointment["patient_name"] ?? "-") ?></td>
                                        <?php endif; ?>

                                        <?php if ($role !== "doctor"): ?>
                                            <td><?= sanitize($appointment["doctor_name"] ?? "-") ?></td>
                                        <?php endif; ?>

                                        <td><?= sanitize($appointment["specialization_name"] ?? "-") ?></td>
                                        <td><?= sanitize($appointment["appt_date"]) ?></td>
                                        <td><?= sanitize(formatTime($appointment["appt_time"])) ?></td>

                                        <td>
                                            <?php
                                            $badge = "secondary";
                                            if ($appointment["status"] === "pending") $badge = "warning";
                                            if ($appointment["status"] === "confirmed") $badge = "info";
                                            if ($appointment["status"] === "completed") $badge = "success";
                                            if ($appointment["status"] === "cancelled") $badge = "danger";
                                            ?>

                                            <span class="badge badge-<?= $badge ?>">
                                                <?= sanitize($appointment["status"]) ?>
                                            </span>
                                        </td>

                                        <td><?= sanitize($appointment["reason"] ?? "-") ?></td>
                                        <td><?= sanitize($appointment["doctor_notes"] ?? "-") ?></td>

                                        <td>
                                            <?php if ($role === "patient" && $appointment["status"] === "pending"): ?>
                                                <form method="POST" action="<?= BASE_URL ?>index.php?page=appointments&action=cancel" style="display:inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="appointment_id" value="<?= sanitize($appointment["id"]) ?>">

                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Cancel
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <?php
                                            $hasPrescription = isset($prescriptionMap[(int) $appointment["id"]]);
                                            ?>

                                            <?php if ($role === "doctor" && $appointment["status"] === "completed" && !$hasPrescription): ?>
                                                <a
                                                    href="<?= BASE_URL ?>index.php?page=prescriptions&action=add&appointment_id=<?= sanitize($appointment["id"]) ?>"
                                                    class="btn btn-sm btn-success mb-1">
                                                    Add Prescription
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($role === "doctor" && $appointment["status"] === "completed" && $hasPrescription): ?>
                                                <span class="badge badge-success d-block mb-1">
                                                    Prescription Added
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($role === "admin" || $role === "doctor"): ?>
                                                <form method="POST" action="<?= BASE_URL ?>index.php?page=appointments&action=updateStatus">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="appointment_id" value="<?= sanitize($appointment["id"]) ?>">

                                                    <div class="form-group mb-1">
                                                        <select name="status" class="form-control form-control-sm">
                                                            <?php foreach (["pending", "confirmed", "completed", "cancelled"] as $status): ?>
                                                                <option value="<?= $status ?>" <?= $appointment["status"] === $status ? "selected" : "" ?>>
                                                                    <?= ucfirst($status) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group mb-1">
                                                        <input type="text" name="doctor_notes" class="form-control form-control-sm" placeholder="Notes">
                                                    </div>

                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        Update
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
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