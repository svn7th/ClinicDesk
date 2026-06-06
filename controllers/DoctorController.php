<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/DoctorModel.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class DoctorController
{
    public function index()
    {
        Auth::requireRole("admin");

        $doctorModel = new DoctorModel();
        $doctors = $doctorModel->getAll();

        require_once __DIR__ . "/../views/doctors/index.php";
    }

    public function create()
    {
        Auth::requireRole("admin");

        $userModel = new UserModel();
        $specializationModel = new SpecializationModel();

        $doctorUsers = $userModel->getUsersByRole("doctor");
        $specializations = $specializationModel->getAll();

        require_once __DIR__ . "/../views/doctors/create.php";
    }

    public function store()
    {
        Auth::requireRole("admin");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=doctors&action=create");
        }

        $userId = (int) ($_POST["user_id"] ?? 0);
        $specializationId = (int) ($_POST["specialization_id"] ?? 0);
        $bio = trim($_POST["bio"] ?? "");
        $fee = (float) ($_POST["consultation_fee"] ?? 0);
        $days = $_POST["available_days"] ?? [];

        if ($userId <= 0 || $specializationId <= 0 || empty($days)) {
            flash("error", "Doctor user, specialization, and available days are required.");
            redirect("index.php?page=doctors&action=create");
        }

        $doctorModel = new DoctorModel();

        if ($doctorModel->findByUserId($userId)) {
            flash("error", "This user already has a doctor record.");
            redirect("index.php?page=doctors&action=create");
        }

        $doctorModel->create([
            "user_id" => $userId,
            "specialization_id" => $specializationId,
            "bio" => $bio,
            "consultation_fee" => $fee,
            "available_days" => implode(",", $days)
        ]);

        flash("success", "Doctor record created successfully.");
        redirect("index.php?page=doctors");
    }

    public function edit()
    {
        Auth::requireRole("admin");

        $id = (int) ($_GET["id"] ?? 0);

        if ($id <= 0) {
            flash("error", "Invalid doctor.");
            redirect("index.php?page=doctors");
        }

        $doctorModel = new DoctorModel();
        $specializationModel = new SpecializationModel();

        $doctor = $doctorModel->findById($id);

        if (!$doctor) {
            flash("error", "Doctor not found.");
            redirect("index.php?page=doctors");
        }

        $specializations = $specializationModel->getAll();
        $selectedDays = explode(",", $doctor["available_days"]);

        require_once __DIR__ . "/../views/doctors/edit.php";
    }

    public function updateDoctor()
    {
        Auth::requireRole("admin");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=doctors");
        }

        $id = (int) ($_POST["id"] ?? 0);
        $specializationId = (int) ($_POST["specialization_id"] ?? 0);
        $bio = trim($_POST["bio"] ?? "");
        $fee = (float) ($_POST["consultation_fee"] ?? 0);
        $days = $_POST["available_days"] ?? [];

        if ($id <= 0 || $specializationId <= 0 || empty($days)) {
            flash("error", "Specialization and available days are required.");
            redirect("index.php?page=doctors");
        }

        $doctorModel = new DoctorModel();

        if (!$doctorModel->findById($id)) {
            flash("error", "Doctor not found.");
            redirect("index.php?page=doctors");
        }

        $existingDoctor = $doctorModel->findById($id);
        $photoPath = $existingDoctor["photo"] ?? null;

        if (isset($_POST["remove_photo"]) && $_POST["remove_photo"] === "1") {
            if (!empty($photoPath)) {
                $oldFile = __DIR__ . "/../" . $photoPath;

                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $photoPath = null;
        }

        if (!empty($_FILES["photo"]["name"])) {
            if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
                flash("error", "Photo upload failed.");
                redirect("index.php?page=doctors&action=edit&id=" . $id);
            }

            if ($_FILES["photo"]["size"] > MAX_AVATAR_SIZE) {
                flash("error", "Photo must be 1 MB or smaller.");
                redirect("index.php?page=doctors&action=edit&id=" . $id);
            }

            if (!getimagesize($_FILES["photo"]["tmp_name"])) {
                flash("error", "Only valid image files are allowed.");
                redirect("index.php?page=doctors&action=edit&id=" . $id);
            }

            $extension = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));

            if (!in_array($extension, ["jpg", "jpeg", "png"])) {
                flash("error", "Only JPG and PNG images are allowed.");
                redirect("index.php?page=doctors&action=edit&id=" . $id);
            }

            if (!empty($existingDoctor["photo"])) {
                $oldFile = __DIR__ . "/../" . $existingDoctor["photo"];

                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $uploadDir = __DIR__ . "/../public/uploads/doctor_photos/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = "doctor_" . $id . "_" . time() . "." . $extension;
            $destination = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $destination)) {
                flash("error", "Could not save uploaded photo.");
                redirect("index.php?page=doctors&action=edit&id=" . $id);
            }

            $photoPath = "public/uploads/doctor_photos/" . $fileName;
        }

        $doctorModel->update($id, [
            "specialization_id" => $specializationId,
            "bio" => $bio,
            "consultation_fee" => $fee,
            "available_days" => implode(",", $days),
            "photo" => $photoPath
        ]);

        flash("success", "Doctor updated successfully.");
        redirect("index.php?page=doctors");
    }
}