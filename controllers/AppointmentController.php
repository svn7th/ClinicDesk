<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../core/Paginator.php";

class AppointmentController
{
    public function book()
    {
        Auth::requireRole("patient");

        $doctorModel = new DoctorModel();
        $doctors = $doctorModel->getAll();

        require_once __DIR__ . "/../views/appointments/book.php";
    }

    public function store()
    {
        Auth::requireRole("patient");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=appointments&action=book");
        }

        $doctorId = (int) ($_POST["doctor_id"] ?? 0);
        $date = $_POST["appt_date"] ?? "";
        $time = $_POST["appt_time"] ?? "";
        $reason = trim($_POST["reason"] ?? "");

        if ($doctorId <= 0 || $date === "" || $time === "") {
            flash("error", "Doctor, date, and time are required.");
            redirect("index.php?page=appointments&action=book");
        }

        if ($date < date("Y-m-d")) {
            flash("error", "Appointment date cannot be in the past.");
            redirect("index.php?page=appointments&action=book");
        }

        $doctorModel = new DoctorModel();
        $availableDays = $doctorModel->getAvailableDays($doctorId);

        $dayName = date("D", strtotime($date));

        if (!in_array($dayName, $availableDays)) {
            flash("error", "This doctor is not available on the selected day.");
            redirect("index.php?page=appointments&action=book");
        }

        $appointmentModel = new AppointmentModel();

        if ($appointmentModel->hasConflict($doctorId, $date, $time)) {
            flash("error", "This slot is already booked. Please choose another time.");
            redirect("index.php?page=appointments&action=book");
        }

        $appointmentModel->book([
            "patient_id" => Auth::currentUser()["id"],
            "doctor_id" => $doctorId,
            "appt_date" => $date,
            "appt_time" => $time,
            "reason" => $reason
        ]);

        flash("success", "Appointment booked successfully.");
        redirect("index.php?page=appointments");
    }

    public function index()
    {
        if (!Auth::check()) {
            redirect("index.php?page=login");
        }

        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();

        $user = Auth::currentUser();
        $role = Auth::role();

        $page = isset($_GET["p"]) ? (int)$_GET["p"] : 1;

        $filters = [
            "status" => $_GET["status"] ?? "",
            "start_date" => $_GET["start_date"] ?? "",
            "end_date" => $_GET["end_date"] ?? "",
            "doctor_id" => $_GET["doctor_id"] ?? "",
            "patient_name" => trim($_GET["patient_name"] ?? "")
        ];

        if ($filters["start_date"] !== "" && $filters["end_date"] !== "" && $filters["start_date"] > $filters["end_date"]) {
            flash("error", "Start date must be before or equal to end date.");
            redirect("index.php?page=appointments");
        }

        if ($role === "patient") {
            $totalAppointments = $appointmentModel->countFiltered("patient", $user["id"], $filters);
            $paginator = new Paginator($totalAppointments, ITEMS_PER_PAGE, $page);
            $appointments = $appointmentModel->getByPatient($user["id"], $page, $filters);
        } elseif ($role === "doctor") {
            $doctorId = $doctorModel->findDoctorIdByUserId($user["id"]);

            if (!$doctorId) {
                flash("error", "Doctor profile not found.");
                redirect("index.php?page=dashboard");
            }

            $totalAppointments = $appointmentModel->countFiltered("doctor", $doctorId, $filters);
            $paginator = new Paginator($totalAppointments, ITEMS_PER_PAGE, $page);
            $appointments = $appointmentModel->getByDoctor($doctorId, $page, $filters);
        } elseif ($role === "admin") {
            $totalAppointments = $appointmentModel->countFiltered("admin", 0, $filters);
            $paginator = new Paginator($totalAppointments, ITEMS_PER_PAGE, $page);
            $appointments = $appointmentModel->getAll($page, $filters);
        } else {
            redirect("index.php?page=error403");
        }

        $doctors = [];

        if ($role === "admin") {
            $doctors = $doctorModel->getAll();
        }

        require_once __DIR__ . "/../views/appointments/index.php";
    }

    public function updateStatus()
    {
        if (!Auth::check()) {
            redirect("index.php?page=login");
        }

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=appointments");
        }

        $appointmentId = (int) ($_POST["id"] ?? 0);
        $newStatus = $_POST["status"] ?? "";

        $allowedStatuses = ["confirmed", "completed", "cancelled"];

        if ($appointmentId <= 0 || !in_array($newStatus, $allowedStatuses)) {
            flash("error", "Invalid appointment action.");
            redirect("index.php?page=appointments");
        }

        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();

        $appointment = $appointmentModel->findById($appointmentId);

        if (!$appointment) {
            flash("error", "Appointment not found.");
            redirect("index.php?page=appointments");
        }

        $role = Auth::role();
        $user = Auth::currentUser();

        if ($role === "patient") {
            if ((int)$appointment["patient_id"] !== (int)$user["id"]) {
                redirect("index.php?page=error403");
            }

            if ($appointment["status"] !== "pending" || $newStatus !== "cancelled") {
                flash("error", "You can only cancel pending appointments.");
                redirect("index.php?page=appointments");
            }
        } elseif ($role === "doctor") {
            $doctorId = $doctorModel->findDoctorIdByUserId($user["id"]);

            if (!$doctorId || (int)$appointment["doctor_id"] !== (int)$doctorId) {
                redirect("index.php?page=error403");
            }
        } elseif ($role !== "admin") {
            redirect("index.php?page=error403");
        }

        $currentStatus = $appointment["status"];

        $validTransition = false;

        if ($currentStatus === "pending" && in_array($newStatus, ["confirmed", "cancelled"])) {
            $validTransition = true;
        }

        if ($currentStatus === "confirmed" && in_array($newStatus, ["completed", "cancelled"])) {
            $validTransition = true;
        }

        if (!$validTransition) {
            flash("error", "Invalid status change.");
            redirect("index.php?page=appointments");
        }

        $appointmentModel->updateStatus($appointmentId, $newStatus);

        flash("success", "Appointment status updated successfully.");
        redirect("index.php?page=appointments");
    }
}