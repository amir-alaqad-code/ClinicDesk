<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/helpers.php";
require_once __DIR__ . "/../../core/CSRF.php";

Auth::requireRole("admin");

// Store the selected user in a separate variable before loading sidebar
$editUser = $user;

require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";

$userName = $editUser["name"] ?? "";
$userEmail = $editUser["email"] ?? "";
$userRole = $editUser["role"] ?? "";
$userPhone = $editUser["phone"] ?? "";
$userStatus = (int) ($editUser["is_active"] ?? 0);
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit User</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        Edit <?= sanitize($userName) ?>
                    </h3>
                </div>

                <form method="POST" action="<?= BASE_URL ?>index.php?page=users&action=update">
                    <div class="card-body">

                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="id" value="<?= sanitize($editUser["id"] ?? "") ?>">

                        <div class="form-group">
                            <label>Name</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?= sanitize($userName) ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input
                                type="email"
                                class="form-control"
                                value="<?= sanitize($userEmail) ?>"
                                disabled>
                            <small class="text-muted">
                                Email cannot be changed from this form.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <input
                                type="text"
                                class="form-control"
                                value="<?= sanitize($userRole) ?>"
                                disabled>
                            <small class="text-muted">
                                Role is managed during account creation.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Phone</label>
                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?= sanitize($userPhone) ?>">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <div>
                                <?php if ($userStatus === 1): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                Status is changed using the Toggle button in the users list.
                            </small>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>

                        <a href="<?= BASE_URL ?>index.php?page=users" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>