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
                <h1>Doctors</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="<?= BASE_URL ?>index.php?page=dashboard">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Doctors</li>
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
                    <h3 class="card-title mb-0">Doctors List</h3>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Doctor Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Specialization</th>
                                    <th>Fee</th>
                                    <th>Available Days</th>
                                    <th>Status</th>
                                    <th width="120">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($doctors)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            No doctors found.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($doctors as $doctor): ?>
                                    <tr>
                                        <td><?= sanitize($doctor["id"]) ?></td>
                                        <td><?= sanitize($doctor["name"]) ?></td>
                                        <td><?= sanitize($doctor["email"]) ?></td>
                                        <td><?= sanitize($doctor["phone"] ?? "-") ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= sanitize($doctor["specialization_name"]) ?>
                                            </span>
                                        </td>
                                        <td><?= sanitize($doctor["consultation_fee"]) ?></td>
                                        <td><?= sanitize($doctor["available_days"]) ?></td>
                                        <td>
                                            <?php if ((int) $doctor["is_active"] === 1): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>index.php?page=doctors&action=edit&id=<?= sanitize($doctor["id"]) ?>" class="btn btn-sm btn-warning">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <p class="text-muted">
                        Doctors are created from the Users panel by selecting role Doctor.
                    </p>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>