<?php

require_once __DIR__ . "/../../core/Auth.php";

$user = Auth::currentUser();
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <!-- Sidebar toggle button -->
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= BASE_URL ?>index.php?page=dashboard" class="nav-link">Dashboard</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <?php if ($user): ?>
            <li class="nav-item">
                <span class="nav-link">
                    <?= sanitize($user["name"]) ?> (<?= sanitize($user["role"]) ?>)
                </span>
            </li>
        <?php endif; ?>
    </ul>
</nav>