<?php

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class ReportController
{
    // Show reports page and export CSV
    public function index(): void
    {
        Auth::requireRole("admin");

        $pageTitle = "Reports";

        $doctorModel = new DoctorModel();
        $appointmentModel = new AppointmentModel();

        $doctors = $doctorModel->getAll();

        $filters = [
            "start_date" => $_GET["start_date"] ?? "",
            "end_date" => $_GET["end_date"] ?? "",
            "doctor_id" => isset($_GET["doctor_id"]) ? (int) $_GET["doctor_id"] : 0,
            "status" => $_GET["status"] ?? ""
        ];

        $reportRows = [];
        $statusCounts = [];

        if ($filters["start_date"] !== "" && $filters["end_date"] !== "") {
            if ($filters["start_date"] > $filters["end_date"]) {
                setFlash("error", "Start date must be before or equal to end date.");
                redirect(BASE_URL . "index.php?page=reports");
            }

            $reportRows = $appointmentModel->getReport($filters);

            foreach ($reportRows as $row) {
                $status = $row["status"];

                if (!isset($statusCounts[$status])) {
                    $statusCounts[$status] = 0;
                }

                $statusCounts[$status]++;
            }

            if (($_GET["export"] ?? "") === "csv") {
                $this->exportCsv($reportRows);
            }
        }

        require_once __DIR__ . "/../views/reports/index.php";
    }

    // Export report rows as CSV
    private function exportCsv(array $rows): void
    {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"appointments_report.csv\"");

        $output = fopen("php://output", "w");

        fputcsv($output, [
            "Patient Name",
            "Doctor Name",
            "Specialization",
            "Date",
            "Time",
            "Status",
            "Reason"
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row["patient_name"],
                $row["doctor_name"],
                $row["specialization_name"],
                $row["appt_date"],
                $row["appt_time"],
                $row["status"],
                $row["reason"]
            ]);
        }

        fclose($output);
        exit;
    }
}