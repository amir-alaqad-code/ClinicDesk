<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";
require_once __DIR__ . "/../../core/CSRF.php";

Auth::requireRole("admin");

$csrfToken = CSRF::generateToken();

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Specializations</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Add Specialization</h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>index.php?page=specializations&action=store">
                    <div class="card-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                        <div class="form-group">
                            <label>Specialization Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Add Specialization
                        </button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Specializations List</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($specializations)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No specializations found.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($specializations as $specialization): ?>
                                <tr>
                                    <td><?= sanitize($specialization["id"]) ?></td>
                                    <td><?= sanitize($specialization["name"]) ?></td>
                                    <td>
                                        <form method="POST" action="<?= BASE_URL ?>index.php?page=specializations&action=delete" style="display:inline-block;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <input type="hidden" name="id" value="<?= sanitize($specialization["id"]) ?>">

                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p class="text-muted mt-3">
                        A specialization cannot be deleted if it is already assigned to a doctor.
                    </p>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>