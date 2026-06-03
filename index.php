<?php

// Start the session because authentication depends on session data
session_start();

// Load the main project configuration
require_once __DIR__ . "/config/config.php";
// Load shared helper functions
require_once __DIR__ . "/core/helpers.php";


// Get the requested page from the URL, or use dashboard as the default page
$page = $_GET["page"] ?? "dashboard";

// Get the requested action from the URL, or use index as the default action
$action = $_GET["action"] ?? "index";

// Define allowed pages and map each page to its controller
$allowedPages = [
    "auth" => "AuthController",
    "dashboard" => "DashboardController",
    "users" => "UserController",
    "doctors" => "DoctorController",
    "appointments" => "AppointmentController",
    "prescriptions" => "PrescriptionController",
    "specializations" => "SpecializationController",
    "reports" => "ReportController"
];

// Show 404 if the requested page is not allowed
if (!array_key_exists($page, $allowedPages)) {
    require_once __DIR__ . "/views/errors/404.php";
    exit;
}

// Get the controller name for the requested page
$controllerName = $allowedPages[$page];

// Build the full path of the controller file
$controllerFile = __DIR__ . "/controllers/" . $controllerName . ".php";

// Show 404 if the controller file does not exist
if (!file_exists($controllerFile)) {
    require_once __DIR__ . "/views/errors/404.php";
    exit;
}

// Load the controller file
require_once $controllerFile;

// Create an object from the selected controller
$controller = new $controllerName();

// Show 404 if the requested action does not exist in the controller
if (!method_exists($controller, $action)) {
    require_once __DIR__ . "/views/errors/404.php";
    exit;
}

// Run the requested controller action
$controller->$action();