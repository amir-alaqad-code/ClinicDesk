<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class DoctorController
{
    // Show doctors list for admin only
    public function index(): void
    {
        Auth::requireRole("admin");

        $pageTitle = "Doctors";

        // Get current page number from URL
        $currentPage = isset($_GET["p"]) ? max(1, (int) $_GET["p"]) : 1;

        $doctorModel = new DoctorModel();

        // Get doctors list with pagination
        $doctors = $doctorModel->getAllPaginated($currentPage);

        require_once __DIR__ . "/../views/doctors/index.php";
    }

    // Show edit doctor form
    public function edit(): void
    {
        Auth::requireRole("admin");

        $doctorId = (int) ($_GET["id"] ?? 0);

        if ($doctorId <= 0) {
            setFlash("error", "Invalid doctor ID.");
            redirect(BASE_URL . "index.php?page=doctors");
        }

        $doctorModel = new DoctorModel();
        $specializationModel = new SpecializationModel();

        $doctors = $doctorModel->getAllPaginated(1);
        $doctor = null;

        foreach ($doctors as $item) {
            if ((int) $item["id"] === $doctorId) {
                $doctor = $item;
                break;
            }
        }

        if (!$doctor) {
            setFlash("error", "Doctor not found.");
            redirect(BASE_URL . "index.php?page=doctors");
        }

        $specializations = $specializationModel->getAll();

        $pageTitle = "Edit Doctor";

        require_once __DIR__ . "/../views/doctors/edit.php";
    }

    // Update doctor data
    public function update(): void
    {
        Auth::requireRole("admin");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            setFlash("error", "Invalid request. Please try again.");
            redirect(BASE_URL . "index.php?page=doctors");
        }

        $doctorId = (int) ($_POST["doctor_id"] ?? 0);
        $specializationId = (int) ($_POST["specialization_id"] ?? 0);
        $bio = trim($_POST["bio"] ?? "");
        $consultationFee = (float) ($_POST["consultation_fee"] ?? 0);
        $availableDays = $_POST["available_days"] ?? [];

        if ($doctorId <= 0 || $specializationId <= 0) {
            setFlash("error", "Doctor and specialization are required.");
            redirect(BASE_URL . "index.php?page=doctors");
        }

        if ($consultationFee < 0) {
            setFlash("error", "Consultation fee cannot be negative.");
            redirect(BASE_URL . "index.php?page=doctors&action=edit&id=" . $doctorId);
        }

        if (empty($availableDays)) {
            setFlash("error", "Please select at least one available day.");
            redirect(BASE_URL . "index.php?page=doctors&action=edit&id=" . $doctorId);
        }

        $doctorModel = new DoctorModel();

        $success = $doctorModel->update($doctorId, [
            "specialization_id" => $specializationId,
            "bio" => $bio,
            "consultation_fee" => $consultationFee,
            "available_days" => implode(",", $availableDays)
        ]);

        if (!$success) {
            setFlash("error", "Failed to update doctor.");
            redirect(BASE_URL . "index.php?page=doctors&action=edit&id=" . $doctorId);
        }

        setFlash("success", "Doctor updated successfully.");
        redirect(BASE_URL . "index.php?page=doctors");
    }
}