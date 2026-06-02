<?php

require_once __DIR__ . "/../../core/Auth.php";
require_once __DIR__ . "/../../core/CSRF.php";

$user = Auth::currentUser();
$role = Auth::role();

$currentPage = $_GET["page"] ?? "dashboard";

function activeLink(string $pageName, string $currentPage): string
{
    return $pageName === $currentPage ? "active" : "";
}
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= BASE_URL ?>index.php?page=dashboard" class="brand-link">
        <span class="brand-text font-weight-light ml-2">ClinicDesk</span>
    </a>

    <div class="sidebar">
        <!-- User panel -->
        <?php if ($user): ?>
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block">
                        <?= sanitize($user["name"]) ?>
                    </a>
                    <small class="text-muted">
                        <?= sanitize($role) ?>
                    </small>
                </div>
            </div>
        <?php endif; ?>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <li class="nav-item">
                    <a href="<?= BASE_URL ?>index.php?page=dashboard" class="nav-link <?= activeLink("dashboard", $currentPage) ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if ($role === "admin"): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?page=users" class="nav-link <?= activeLink("users", $currentPage) ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?page=doctors" class="nav-link <?= activeLink("doctors", $currentPage) ?>">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Doctors</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?page=appointments" class="nav-link <?= activeLink("appointments", $currentPage) ?>">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Appointments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?page=reports" class="nav-link <?= activeLink("reports", $currentPage) ?>">
                            <i class="nav-icon fas fa-file-csv"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === "doctor"): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?page=appointments" class="nav-link <?= activeLink("appointments", $currentPage) ?>">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>My Schedule</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === "patient"): ?>
                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?page=appointments&action=book" class="nav-link">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Book Appointment</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= BASE_URL ?>index.php?page=prescriptions" class="nav-link <?= activeLink("prescriptions", $currentPage) ?>">
                            <i class="nav-icon fas fa-file-medical"></i>
                            <p>My Prescriptions</p>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item mt-3">
                    <form method="POST" action="<?= BASE_URL ?>index.php?page=auth&action=logout" class="px-3">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <button type="submit" class="btn btn-danger btn-sm btn-block">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </button>
                    </form>
                </li>

            </ul>
        </nav>
    </div>
</aside>