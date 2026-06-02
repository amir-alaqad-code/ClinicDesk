<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";

class DashboardController
{
    // Redirect logged-in users to the correct dashboard by role
    public function index(): void
    {
        if (!Auth::check()) {
            redirect(BASE_URL . "index.php?page=auth&action=login");
        }

        $role = Auth::role();

        if ($role === "admin") {
            $this->admin();
            return;
        }

        if ($role === "doctor") {
            $this->doctor();
            return;
        }

        if ($role === "patient") {
            $this->patient();
            return;
        }

        require_once __DIR__ . "/../views/errors/403.php";
    }

    // Admin dashboard statistics
    private function admin(): void
    {
        Auth::requireRole("admin");

        $pageTitle = "Admin Dashboard";

        $userModel = new UserModel();
        $appointmentModel = new AppointmentModel();

        $usersByRole = $userModel->countByRole();
        $appointmentsToday = $appointmentModel->countToday();
        $weekStatusCounts = $appointmentModel->countThisWeekByStatus();
        $recentAppointments = $appointmentModel->getRecentFive();

        require_once __DIR__ . "/../views/dashboard/admin.php";
    }

    // Doctor dashboard statistics
    private function doctor(): void
    {
        Auth::requireRole("doctor");

        $pageTitle = "Doctor Dashboard";

        $doctorModel = new DoctorModel();
        $appointmentModel = new AppointmentModel();

        $doctor = $doctorModel->findByUserId((int) Auth::currentUser()["id"]);

        if (!$doctor) {
            setFlash("error", "Doctor profile not found.");
            redirect(BASE_URL . "index.php?page=auth&action=logout");
        }

        $totalThisMonth = $appointmentModel->countDoctorThisMonth((int) $doctor["id"]);
        $pendingCount = $appointmentModel->countDoctorByStatus((int) $doctor["id"], "pending");
        $completedCount = $appointmentModel->countDoctorByStatus((int) $doctor["id"], "completed");
        $upcomingAppointments = $appointmentModel->getDoctorUpcoming((int) $doctor["id"]);

        require_once __DIR__ . "/../views/dashboard/doctor.php";
    }

    // Patient dashboard statistics
    private function patient(): void
    {
        Auth::requireRole("patient");

        $pageTitle = "Patient Dashboard";

        $appointmentModel = new AppointmentModel();
        $prescriptionModel = new PrescriptionModel();

        $patientId = (int) Auth::currentUser()["id"];

        $activeAppointments = $appointmentModel->countPatientActive($patientId);
        $completedAppointments = $appointmentModel->countPatientCompleted($patientId);
        $prescriptionsCount = $prescriptionModel->countByPatient($patientId);
        $nextAppointment = $appointmentModel->getPatientNextAppointment($patientId);

        require_once __DIR__ . "/../views/dashboard/patient.php";
    }
}