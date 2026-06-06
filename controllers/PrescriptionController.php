<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/AppointmentModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/PrescriptionModel.php";

class PrescriptionController
{
    public function create()
    {
        Auth::requireRole("doctor", "admin");

        $appointmentId = (int) ($_GET["appointment_id"] ?? 0);

        if ($appointmentId <= 0) {
            flash("error", "Invalid appointment.");
            redirect("index.php?page=appointments");
        }

        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();
        $prescriptionModel = new PrescriptionModel();

        $appointment = $appointmentModel->findById($appointmentId);

        if (!$appointment) {
            flash("error", "Appointment not found.");
            redirect("index.php?page=appointments");
        }

        if ($appointment["status"] !== "completed") {
            flash("error", "Prescription can only be added after appointment is completed.");
            redirect("index.php?page=appointments");
        }

        if (Auth::role() === "doctor") {
            $doctorId = $doctorModel->findDoctorIdByUserId(Auth::currentUser()["id"]);

            if (!$doctorId || (int)$appointment["doctor_id"] !== (int)$doctorId) {
                redirect("index.php?page=error403");
            }
        }

        if ($prescriptionModel->findByAppointmentId($appointmentId)) {
            flash("error", "Prescription already exists for this appointment.");
            redirect("index.php?page=appointments");
        }

        require_once __DIR__ . "/../views/prescriptions/create.php";
    }

    public function store()
    {
        Auth::requireRole("doctor", "admin");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=appointments");
        }

        $appointmentId = (int) ($_POST["appointment_id"] ?? 0);
        $diagnosis = trim($_POST["diagnosis"] ?? "");
        $medications = trim($_POST["medications"] ?? "");
        $notes = trim($_POST["notes"] ?? "");

        if ($appointmentId <= 0 || $diagnosis === "" || $medications === "") {
            flash("error", "Diagnosis and medications are required.");
            redirect("index.php?page=appointments");
        }

        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();
        $prescriptionModel = new PrescriptionModel();

        $appointment = $appointmentModel->findById($appointmentId);

        if (!$appointment) {
            flash("error", "Appointment not found.");
            redirect("index.php?page=appointments");
        }

        if ($appointment["status"] !== "completed") {
            flash("error", "Appointment must be completed first.");
            redirect("index.php?page=appointments");
        }

        if (Auth::role() === "doctor") {
            $doctorId = $doctorModel->findDoctorIdByUserId(Auth::currentUser()["id"]);

            if (!$doctorId || (int)$appointment["doctor_id"] !== (int)$doctorId) {
                redirect("index.php?page=error403");
            }
        }

        if ($prescriptionModel->findByAppointmentId($appointmentId)) {
            flash("error", "Prescription already exists.");
            redirect("index.php?page=appointments");
        }

        $filePath = null;

        if (!empty($_FILES["prescription_file"]["name"])) {
            if ($_FILES["prescription_file"]["error"] !== UPLOAD_ERR_OK) {
                flash("error", "File upload failed.");
                redirect("index.php?page=prescriptions&action=create&appointment_id=" . $appointmentId);
            }

            if ($_FILES["prescription_file"]["size"] > MAX_PDF_SIZE) {
                flash("error", "PDF file must be 3 MB or smaller.");
                redirect("index.php?page=prescriptions&action=create&appointment_id=" . $appointmentId);
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES["prescription_file"]["tmp_name"]);
            finfo_close($finfo);

            if ($mimeType !== "application/pdf") {
                flash("error", "Only PDF files are allowed.");
                redirect("index.php?page=prescriptions&action=create&appointment_id=" . $appointmentId);
            }

            $uploadDir = __DIR__ . "/../public/uploads/prescriptions/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = "prescription_" . $appointmentId . "_" . time() . ".pdf";
            $destination = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES["prescription_file"]["tmp_name"], $destination)) {
                flash("error", "Could not save uploaded file.");
                redirect("index.php?page=prescriptions&action=create&appointment_id=" . $appointmentId);
            }

            $filePath = "public/uploads/prescriptions/" . $fileName;
        }

        $prescriptionModel->create([
            "appointment_id" => $appointmentId,
            "diagnosis" => $diagnosis,
            "medications" => $medications,
            "notes" => $notes,
            "file_path" => $filePath
        ]);

        flash("success", "Prescription added successfully.");
        redirect("index.php?page=appointments");
    }

    public function index()
    {
        Auth::requireRole("patient");

        $prescriptionModel = new PrescriptionModel();

        $prescriptions = $prescriptionModel->getByPatient(Auth::currentUser()["id"]);

        require_once __DIR__ . "/../views/prescriptions/index.php";
    }

    public function download()
    {
        if (!Auth::check()) {
            redirect("index.php?page=login");
        }

        $id = (int) ($_GET["id"] ?? 0);

        if ($id <= 0) {
            flash("error", "Invalid prescription.");
            redirect("index.php?page=dashboard");
        }

        $prescriptionModel = new PrescriptionModel();
        $doctorModel = new DoctorModel();

        $prescription = $prescriptionModel->findByIdWithAppointment($id);

        if (!$prescription) {
            flash("error", "Prescription not found.");
            redirect("index.php?page=dashboard");
        }

        if (empty($prescription["file_path"])) {
            flash("error", "No PDF file attached to this prescription.");
            redirect("index.php?page=dashboard");
        }

        $role = Auth::role();
        $user = Auth::currentUser();

        if ($role === "patient") {
            if ((int)$prescription["patient_id"] !== (int)$user["id"]) {
                redirect("index.php?page=error403");
            }
        } elseif ($role === "doctor") {
            $doctorId = $doctorModel->findDoctorIdByUserId($user["id"]);

            if (!$doctorId || (int)$prescription["doctor_id"] !== (int)$doctorId) {
                redirect("index.php?page=error403");
            }
        } elseif ($role !== "admin") {
            redirect("index.php?page=error403");
        }

        $fullPath = __DIR__ . "/../" . $prescription["file_path"];

        if (!file_exists($fullPath)) {
            flash("error", "PDF file not found.");
            redirect("index.php?page=dashboard");
        }

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=\"prescription.pdf\"");
        header("Content-Length: " . filesize($fullPath));

        readfile($fullPath);
        exit;
    }
}