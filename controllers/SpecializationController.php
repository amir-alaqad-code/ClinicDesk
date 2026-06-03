<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class SpecializationController
{
    // Show all specializations
    public function index(): void
    {
        Auth::requireRole("admin");

        $pageTitle = "Specializations";

        $specializationModel = new SpecializationModel();
        $specializations = $specializationModel->getAll();

        require_once __DIR__ . "/../views/specializations/index.php";
    }

    // Store a new specialization
    public function store(): void
    {
        Auth::requireRole("admin");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            setFlash("error", "Invalid request. Please try again.");
            redirect(BASE_URL . "index.php?page=specializations");
        }

        $name = trim($_POST["name"] ?? "");

        if ($name === "") {
            setFlash("error", "Specialization name is required.");
            redirect(BASE_URL . "index.php?page=specializations");
        }

        $specializationModel = new SpecializationModel();

        $createdId = $specializationModel->create($name);

        if ($createdId === 0) {
            setFlash("error", "Failed to create specialization. It may already exist.");
            redirect(BASE_URL . "index.php?page=specializations");
        }

        setFlash("success", "Specialization created successfully.");
        redirect(BASE_URL . "index.php?page=specializations");
    }

    // Delete specialization only if it is not used by doctors
    public function delete(): void
    {
        Auth::requireRole("admin");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            setFlash("error", "Invalid request. Please try again.");
            redirect(BASE_URL . "index.php?page=specializations");
        }

        $id = (int) ($_POST["id"] ?? 0);

        if ($id <= 0) {
            setFlash("error", "Invalid specialization ID.");
            redirect(BASE_URL . "index.php?page=specializations");
        }

        $specializationModel = new SpecializationModel();

        if (!$specializationModel->isSafeToDelete($id)) {
            setFlash("error", "Cannot delete this specialization because it is used by doctors.");
            redirect(BASE_URL . "index.php?page=specializations");
        }

        $deleted = $specializationModel->delete($id);

        if (!$deleted) {
            setFlash("error", "Failed to delete specialization.");
            redirect(BASE_URL . "index.php?page=specializations");
        }

        setFlash("success", "Specialization deleted successfully.");
        redirect(BASE_URL . "index.php?page=specializations");
    }
}