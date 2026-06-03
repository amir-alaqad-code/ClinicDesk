<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";

Auth::requireRole("admin", "doctor", "patient");

$role = Auth::role();

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Prescriptions</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Prescriptions</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <?php if ($role !== "patient"): ?>
                <div class="alert alert-info">
                    Prescriptions are managed from completed appointments.
                </div>
            <?php endif; ?>

            <?php if ($role === "patient"): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">My Prescriptions</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Specialization</th>
                                        <th>Date</th>
                                        <th>Diagnosis</th>
                                        <th>PDF</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (empty($prescriptions)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                No prescriptions found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($prescriptions as $prescription): ?>
                                        <tr>
                                            <td><?= sanitize($prescription["doctor_name"]) ?></td>
                                            <td><?= sanitize($prescription["specialization_name"]) ?></td>
                                            <td><?= sanitize($prescription["appt_date"]) ?></td>
                                            <td>
                                                <?= sanitize(mb_substr($prescription["diagnosis"], 0, 80)) ?>
                                                <?= mb_strlen($prescription["diagnosis"]) > 80 ? "..." : "" ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($prescription["file_path"])): ?>
                                                    <a
                                                        href="<?= BASE_URL ?>index.php?page=prescriptions&action=download&id=<?= sanitize($prescription["appointment_id"]) ?>"
                                                        class="btn btn-sm btn-primary">
                                                        Download PDF
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">No PDF</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>