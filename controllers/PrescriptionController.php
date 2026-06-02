<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class PrescriptionController
{
    // Show prescriptions based on role
    public function index(): void
    {
        Auth::requireRole("admin", "doctor", "patient");

        $pageTitle = "Prescriptions";
        $role = Auth::role();

        $prescriptionModel = new PrescriptionModel();

        if ($role === "patient") {
            $prescriptions = $prescriptionModel->getByPatient((int) Auth::currentUser()["id"]);
        } else {
            $prescriptions = [];
        }

        require_once __DIR__ . "/../views/prescriptions/index.php";
    }

    // Show add prescription form
    public function add(): void
    {
        Auth::requireRole("doctor");

        $appointmentId = (int) ($_GET["appointment_id"] ?? 0);

        if ($appointmentId <= 0) {
            setFlash("error", "Invalid appointment ID.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();
        $prescriptionModel = new PrescriptionModel();

        $appointment = $appointmentModel->findById($appointmentId);

        if (!$appointment) {
            setFlash("error", "Appointment not found.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $doctor = $doctorModel->findByUserId((int) Auth::currentUser()["id"]);

        if (!$doctor || (int) $appointment["doctor_id"] !== (int) $doctor["id"]) {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        if ($appointment["status"] !== "completed") {
            setFlash("error", "Prescription can only be added after completing the appointment.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        if ($prescriptionModel->findByAppointmentId($appointmentId)) {
            setFlash("error", "Prescription already exists for this appointment.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $pageTitle = "Add Prescription";

        require_once __DIR__ . "/../views/prescriptions/add.php";
    }

    // Store prescription after form submit
    public function store(): void
    {
        Auth::requireRole("doctor");

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            setFlash("error", "Invalid request. Please try again.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $appointmentId = (int) ($_POST["appointment_id"] ?? 0);
        $diagnosis = trim($_POST["diagnosis"] ?? "");
        $medications = trim($_POST["medications"] ?? "");
        $notes = trim($_POST["notes"] ?? "");

        if ($appointmentId <= 0 || $diagnosis === "" || $medications === "") {
            setFlash("error", "Diagnosis and medications are required.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();
        $prescriptionModel = new PrescriptionModel();

        $appointment = $appointmentModel->findById($appointmentId);

        if (!$appointment) {
            setFlash("error", "Appointment not found.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $doctor = $doctorModel->findByUserId((int) Auth::currentUser()["id"]);

        if (!$doctor || (int) $appointment["doctor_id"] !== (int) $doctor["id"]) {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        if ($appointment["status"] !== "completed") {
            setFlash("error", "Prescription can only be added to completed appointments.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        if ($prescriptionModel->findByAppointmentId($appointmentId)) {
            setFlash("error", "Prescription already exists for this appointment.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $fileName = null;

        if (isset($_FILES["prescription_file"]) && $_FILES["prescription_file"]["error"] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES["prescription_file"]["error"] !== UPLOAD_ERR_OK) {
                setFlash("error", "File upload failed.");
                redirect(BASE_URL . "index.php?page=prescriptions&action=add&appointment_id=" . $appointmentId);
            }

            if ($_FILES["prescription_file"]["size"] > MAX_PRESCRIPTION_SIZE) {
                setFlash("error", "Prescription PDF must not exceed 3 MB.");
                redirect(BASE_URL . "index.php?page=prescriptions&action=add&appointment_id=" . $appointmentId);
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES["prescription_file"]["tmp_name"]);
            finfo_close($finfo);

            if ($mimeType !== "application/pdf") {
                setFlash("error", "Only PDF files are allowed.");
                redirect(BASE_URL . "index.php?page=prescriptions&action=add&appointment_id=" . $appointmentId);
            }

            $fileName = "prescription_" . $appointmentId . "_" . time() . ".pdf";

            $uploadPath = __DIR__ . "/../public/uploads/prescriptions/" . $fileName;

            if (!move_uploaded_file($_FILES["prescription_file"]["tmp_name"], $uploadPath)) {
                setFlash("error", "Failed to save uploaded file.");
                redirect(BASE_URL . "index.php?page=prescriptions&action=add&appointment_id=" . $appointmentId);
            }
        }

        $prescriptionId = $prescriptionModel->create([
            "appointment_id" => $appointmentId,
            "diagnosis" => $diagnosis,
            "medications" => $medications,
            "notes" => $notes,
            "file_path" => $fileName
        ]);

        if ($prescriptionId === 0) {
            setFlash("error", "Failed to create prescription.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        setFlash("success", "Prescription added successfully.");
        redirect(BASE_URL . "index.php?page=appointments");
    }

    // Secure PDF download through PHP, not direct URL
    public function download(): void
    {
        Auth::requireRole("admin", "doctor", "patient");

        $appointmentId = (int) ($_GET["id"] ?? 0);

        if ($appointmentId <= 0) {
            require_once __DIR__ . "/../views/errors/404.php";
            exit;
        }

        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();
        $prescriptionModel = new PrescriptionModel();

        $appointment = $appointmentModel->findById($appointmentId);
        $prescription = $prescriptionModel->findByAppointmentId($appointmentId);

        if (!$appointment || !$prescription || empty($prescription["file_path"])) {
            setFlash("error", "Prescription file not found.");
            redirect(BASE_URL . "index.php?page=prescriptions");
        }

        $role = Auth::role();
        $currentUser = Auth::currentUser();

        if ($role === "patient" && (int) $appointment["patient_id"] !== (int) $currentUser["id"]) {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        if ($role === "doctor") {
            $doctor = $doctorModel->findByUserId((int) $currentUser["id"]);

            if (!$doctor || (int) $appointment["doctor_id"] !== (int) $doctor["id"]) {
                require_once __DIR__ . "/../views/errors/403.php";
                exit;
            }
        }

        $filePath = __DIR__ . "/../public/uploads/prescriptions/" . basename($prescription["file_path"]);

        if (!file_exists($filePath)) {
            setFlash("error", "File is missing from server.");
            redirect(BASE_URL . "index.php?page=prescriptions");
        }

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"prescription.pdf\"");
        header("Content-Length: " . filesize($filePath));

        readfile($filePath);
        exit;
    }
}