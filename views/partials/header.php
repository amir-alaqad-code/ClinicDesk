<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../core/helpers.php";

$pageTitle = $pageTitle ?? APP_NAME;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <!-- Make the layout responsive on all screen sizes -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Dynamic page title -->
    <title><?= sanitize($pageTitle) ?> | <?= APP_NAME ?></title>

    <!-- Font Awesome icons from local AdminLTE files -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">

    <!-- AdminLTE main CSS file from local project assets -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">