<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/ReportModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class ReportController
{
    public function index()
    {
        Auth::requireRole("admin");

        $doctorModel = new DoctorModel();
        $reportModel = new ReportModel();

        $doctors = $doctorModel->getAll();

        $startDate = $_GET["start_date"] ?? "";
        $endDate = $_GET["end_date"] ?? "";
        $doctorId = $_GET["doctor_id"] ?? "";
        $status = $_GET["status"] ?? "";

        $rows = [];
        $summary = [
            "pending" => 0,
            "confirmed" => 0,
            "completed" => 0,
            "cancelled" => 0
        ];

        if ($startDate !== "" && $endDate !== "") {
            if ($startDate > $endDate) {
                flash("error", "Start date must be before or equal to end date.");
                redirect("index.php?page=reports");
            }

            $rows = $reportModel->getAppointmentReport($startDate, $endDate, $doctorId, $status);
            $summary = $reportModel->getStatusSummary($rows);

            if (($_GET["export"] ?? "") === "csv") {
                $this->exportCsv($rows);
            }
        }

        require_once __DIR__ . "/../views/reports/index.php";
    }

    private function exportCsv(array $rows)
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