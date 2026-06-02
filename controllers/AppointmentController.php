<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";

class AppointmentController
{
    // Show appointments based on the logged-in user role
    public function index(): void
    {
        Auth::requireRole("admin", "doctor", "patient");

        $pageTitle = "Appointments";
        $currentUser = Auth::currentUser();
        $role = Auth::role();

        $currentPage = isset($_GET["p"]) ? max(1, (int) $_GET["p"]) : 1;

        $filters = [
            "status" => $_GET["status"] ?? "",
            "start_date" => $_GET["start_date"] ?? "",
            "end_date" => $_GET["end_date"] ?? ""
        ];

        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();

        if ($role === "admin") {
            $filters["doctor_id"] = isset($_GET["doctor_id"]) ? (int) $_GET["doctor_id"] : 0;
            $filters["patient_name"] = trim($_GET["patient_name"] ?? "");

            $appointments = $appointmentModel->getAll($currentPage, $filters);
            $doctors = $doctorModel->getAll();
        } elseif ($role === "doctor") {
            $doctor = $doctorModel->findByUserId((int) $currentUser["id"]);

            if (!$doctor) {
                setFlash("error", "Doctor profile not found.");
                redirect(BASE_URL . "index.php?page=dashboard");
            }

            $appointments = $appointmentModel->getByDoctor((int) $doctor["id"], $currentPage, $filters);
            $doctors = [];
        } else {
            $appointments = $appointmentModel->getByPatient((int) $currentUser["id"], $currentPage, $filters);
            $doctors = [];
        }
        // Build a map to know which appointments already have prescriptions
        $prescriptionModel = new PrescriptionModel();
        $prescriptionMap = [];

        foreach ($appointments as $appointment) {
            $prescription = $prescriptionModel->findByAppointmentId((int) $appointment["id"]);

            if ($prescription) {
                $prescriptionMap[(int) $appointment["id"]] = true;
            }
        }
        require_once __DIR__ . "/../views/appointments/index.php";
    }

    // Show booking form or handle appointment booking
    public function book(): void
    {
        Auth::requireRole("patient");

        $pageTitle = "Book Appointment";

        $doctorModel = new DoctorModel();
        $doctors = $doctorModel->getAll();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->storeBooking();
            return;
        }

        require_once __DIR__ . "/../views/appointments/book.php";
    }

    // Store a new appointment booking
    private function storeBooking(): void
    {
        $csrfToken = $_POST["csrf_token"] ?? "";

        if (!CSRF::validateToken($csrfToken)) {
            setFlash("error", "Invalid request. Please try again.");
            redirect(BASE_URL . "index.php?page=appointments&action=book");
        }

        $currentUser = Auth::currentUser();

        $doctorId = (int) ($_POST["doctor_id"] ?? 0);
        $date = $_POST["appt_date"] ?? "";
        $time = $_POST["appt_time"] ?? "";
        $reason = trim($_POST["reason"] ?? "");

        if ($doctorId <= 0 || $date === "" || $time === "") {
            setFlash("error", "Doctor, date, and time are required.");
            redirect(BASE_URL . "index.php?page=appointments&action=book");
        }

        if (strtotime($date) < strtotime(date("Y-m-d"))) {
            setFlash("error", "Appointment date cannot be in the past.");
            redirect(BASE_URL . "index.php?page=appointments&action=book");
        }

        $doctorModel = new DoctorModel();
        $availableDays = $doctorModel->getAvailableDays($doctorId);

        if (empty($availableDays)) {
            setFlash("error", "Doctor availability was not found.");
            redirect(BASE_URL . "index.php?page=appointments&action=book");
        }

        $selectedDay = date("D", strtotime($date));

        if (!in_array($selectedDay, $availableDays)) {
            setFlash("error", "The selected doctor is not available on this day.");
            redirect(BASE_URL . "index.php?page=appointments&action=book");
        }

        $appointmentModel = new AppointmentModel();

        if ($appointmentModel->hasConflict($doctorId, $date, $time)) {
            setFlash("error", "This slot is already booked, please choose another time.");
            redirect(BASE_URL . "index.php?page=appointments&action=book");
        }

        $success = $appointmentModel->book([
            "patient_id" => (int) $currentUser["id"],
            "doctor_id" => $doctorId,
            "appt_date" => $date,
            "appt_time" => $time,
            "reason" => $reason
        ]);

        if (!$success) {
            setFlash("error", "Failed to book appointment.");
            redirect(BASE_URL . "index.php?page=appointments&action=book");
        }

        setFlash("success", "Appointment booked successfully.");
        redirect(BASE_URL . "index.php?page=appointments");
    }

    // Update appointment status by doctor or admin
    public function updateStatus(): void
    {
        Auth::requireRole("admin", "doctor");

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
        $status = $_POST["status"] ?? "";
        $notes = trim($_POST["doctor_notes"] ?? "");

        if ($appointmentId <= 0 || !in_array($status, ["pending", "confirmed", "completed", "cancelled"])) {
            setFlash("error", "Invalid appointment status.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->findById($appointmentId);

        if (!$appointment) {
            setFlash("error", "Appointment not found.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        if (Auth::role() === "doctor") {
            $doctorModel = new DoctorModel();
            $doctor = $doctorModel->findByUserId((int) Auth::currentUser()["id"]);

            if (!$doctor || (int) $appointment["doctor_id"] !== (int) $doctor["id"]) {
                require_once __DIR__ . "/../views/errors/403.php";
                exit;
            }
        }

        $success = $appointmentModel->updateStatus($appointmentId, $status, $notes);

        if (!$success) {
            setFlash("error", "Failed to update appointment status.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        setFlash("success", "Appointment status updated successfully.");
        redirect(BASE_URL . "index.php?page=appointments");
    }

    // Patient can cancel only pending appointments that belong to them
    public function cancel(): void
    {
        Auth::requireRole("patient");

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

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->findById($appointmentId);

        if (!$appointment) {
            setFlash("error", "Appointment not found.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        if ((int) $appointment["patient_id"] !== (int) Auth::currentUser()["id"]) {
            require_once __DIR__ . "/../views/errors/403.php";
            exit;
        }

        if ($appointment["status"] !== "pending") {
            setFlash("error", "Only pending appointments can be cancelled by patient.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        $success = $appointmentModel->updateStatus($appointmentId, "cancelled");

        if (!$success) {
            setFlash("error", "Failed to cancel appointment.");
            redirect(BASE_URL . "index.php?page=appointments");
        }

        setFlash("success", "Appointment cancelled successfully.");
        redirect(BASE_URL . "index.php?page=appointments");
    }
}
