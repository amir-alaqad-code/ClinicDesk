<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";
require_once __DIR__ . "/../../core/CSRF.php";

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
                    <h1>Users</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>index.php?page=dashboard">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="<?= BASE_URL ?>index.php?page=users&action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Create User
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Users List</h3>
                </div>

                <div class="card-body">

                    <form method="GET" action="<?= BASE_URL ?>index.php" class="mb-3">
                        <input type="hidden" name="page" value="users">

                        <div class="row">
                            <div class="col-md-4">
                                <label>Filter by Role</label>
                                <select name="role" class="form-control">
                                    <option value="" <?= $roleFilter === "" ? "selected" : "" ?>>All Roles</option>
                                    <option value="admin" <?= $roleFilter === "admin" ? "selected" : "" ?>>Admin</option>
                                    <option value="doctor" <?= $roleFilter === "doctor" ? "selected" : "" ?>>Doctor</option>
                                    <option value="patient" <?= $roleFilter === "patient" ? "selected" : "" ?>>Patient</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Search</label>
                                <input
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Search by name or email"
                                    value="<?= sanitize($_GET["search"] ?? "") ?>">
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary btn-block">
                                    Filter
                                </button>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <a href="<?= BASE_URL ?>index.php?page=users" class="btn btn-light btn-block">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                    <?php $currentUser = Auth::currentUser(); ?>
                    <?php $csrfToken = CSRF::generateToken(); ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            No users found.
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= sanitize($user["id"]) ?></td>
                                        <td><?= sanitize($user["name"]) ?></td>
                                        <td><?= sanitize($user["email"]) ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= sanitize($user["role"]) ?>
                                            </span>
                                        </td>
                                        <td><?= sanitize($user["phone"] ?? "-") ?></td>
                                        <td>
                                            <?php if ((int) $user["is_active"] === 1): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= sanitize($user["created_at"]) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>index.php?page=users&action=edit&id=<?= sanitize($user["id"]) ?>" class="btn btn-sm btn-warning">
                                                Edit
                                            </a>

                                            <?php if ((int) $currentUser["id"] !== (int) $user["id"]): ?>
                                                <form method="POST" action="<?= BASE_URL ?>index.php?page=users&action=toggleActive" style="display:inline-block;">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                                    <input type="hidden" name="id" value="<?= sanitize($user["id"]) ?>">

                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        Toggle
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary" disabled>
                                                    Current
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <?php if ($currentPage > 1): ?>
                            <a
                                href="<?= BASE_URL ?>index.php?page=users&p=<?= $currentPage - 1 ?>&role=<?= sanitize($roleFilter) ?>&search=<?= sanitize($_GET["search"] ?? "") ?>"
                                class="btn btn-secondary">
                                Previous
                            </a>
                        <?php endif; ?>

                        <?php if (count($users) === ITEMS_PER_PAGE): ?>
                            <a
                                href="<?= BASE_URL ?>index.php?page=users&p=<?= $currentPage + 1 ?>&role=<?= sanitize($roleFilter) ?>&search=<?= sanitize($_GET["search"] ?? "") ?>"
                                class="btn btn-secondary">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                    <p class="text-muted">
                        Total users: <?= sanitize($totalUsers) ?>
                    </p>

                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>