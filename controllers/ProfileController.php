<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class ProfileController
{
    public function edit()
    {
        if (!Auth::check()) {
            redirect("index.php?page=login");
        }

        $userModel = new UserModel();
        $user = $userModel->findById(Auth::currentUser()["id"]);

        if (!$user) {
            flash("error", "User not found.");
            redirect("index.php?page=dashboard");
        }

        $doctorProfile = null;

        if (Auth::role() === "doctor") {
            $doctorModel = new DoctorModel();
            $doctorId = $doctorModel->findDoctorIdByUserId(Auth::currentUser()["id"]);

            if ($doctorId) {
                $doctorProfile = $doctorModel->findById($doctorId);
            }
        }

        require_once __DIR__ . "/../views/profile/edit.php";
    }

    public function update()
    {
        if (!Auth::check()) {
            redirect("index.php?page=login");
        }

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=profile");
        }

        $name = trim($_POST["name"] ?? "");
        $phone = trim($_POST["phone"] ?? "");

        if ($name === "") {
            flash("error", "Name is required.");
            redirect("index.php?page=profile");
        }

        $userModel = new UserModel();

        $currentUser = $userModel->findById(Auth::currentUser()["id"]);

        if (!$currentUser) {
            flash("error", "User not found.");
            redirect("index.php?page=dashboard");
        }

        $avatarPath = $currentUser["avatar"] ?? null;

        if (Auth::role() !== "doctor") {
            if (isset($_POST["remove_avatar"]) && $_POST["remove_avatar"] === "1") {
                if (!empty($avatarPath)) {
                    $oldFile = __DIR__ . "/../" . $avatarPath;

                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $avatarPath = null;
            }
            

            if (!empty($_FILES["avatar"]["name"])) {
                if ($_FILES["avatar"]["error"] !== UPLOAD_ERR_OK) {
                    flash("error", "Avatar upload failed.");
                    redirect("index.php?page=profile");
                }

                if ($_FILES["avatar"]["size"] > MAX_AVATAR_SIZE) {
                    flash("error", "Avatar must be 1 MB or smaller.");
                    redirect("index.php?page=profile");
                }

                if (!getimagesize($_FILES["avatar"]["tmp_name"])) {
                    flash("error", "Only valid image files are allowed.");
                    redirect("index.php?page=profile");
                }

                $extension = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));

                if (!in_array($extension, ["jpg", "jpeg", "png"])) {
                    flash("error", "Only JPG and PNG images are allowed.");
                    redirect("index.php?page=profile");
                }

                if (!empty($currentUser["avatar"])) {
                    $oldFile = __DIR__ . "/../" . $currentUser["avatar"];

                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $uploadDir = __DIR__ . "/../public/uploads/avatars/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileName = "avatar_" . Auth::currentUser()["id"] . "_" . time() . "." . $extension;
                $destination = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $destination)) {
                    flash("error", "Could not save uploaded avatar.");
                    redirect("index.php?page=profile");
                }

                $avatarPath = "public/uploads/avatars/" . $fileName;
            }
        }

        if (Auth::role() === "doctor") {
            $doctorModel = new DoctorModel();
            $doctorId = $doctorModel->findDoctorIdByUserId(Auth::currentUser()["id"]);

            if ($doctorId) {
                $doctorProfile = $doctorModel->findById($doctorId);
                $doctorPhotoPath = $doctorProfile["photo"] ?? null;

                if (isset($_POST["remove_doctor_photo"]) && $_POST["remove_doctor_photo"] === "1") {
                    if (!empty($doctorPhotoPath)) {
                        $oldFile = __DIR__ . "/../" . $doctorPhotoPath;

                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }

                    $doctorPhotoPath = null;
                }

                if (!empty($_FILES["doctor_photo"]["name"])) {
                    if ($_FILES["doctor_photo"]["error"] !== UPLOAD_ERR_OK) {
                        flash("error", "Doctor photo upload failed.");
                        redirect("index.php?page=profile");
                    }

                    if ($_FILES["doctor_photo"]["size"] > MAX_AVATAR_SIZE) {
                        flash("error", "Doctor photo must be 1 MB or smaller.");
                        redirect("index.php?page=profile");
                    }

                    if (!getimagesize($_FILES["doctor_photo"]["tmp_name"])) {
                        flash("error", "Only valid image files are allowed for doctor photo.");
                        redirect("index.php?page=profile");
                    }

                    $extension = strtolower(pathinfo($_FILES["doctor_photo"]["name"], PATHINFO_EXTENSION));

                    if (!in_array($extension, ["jpg", "jpeg", "png"])) {
                        flash("error", "Only JPG and PNG images are allowed for doctor photo.");
                        redirect("index.php?page=profile");
                    }

                    if (!empty($doctorProfile["photo"])) {
                        $oldFile = __DIR__ . "/../" . $doctorProfile["photo"];

                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }

                    $uploadDir = __DIR__ . "/../public/uploads/doctor_photos/";

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = "doctor_" . $doctorId . "_" . time() . "." . $extension;
                    $destination = $uploadDir . $fileName;

                    if (!move_uploaded_file($_FILES["doctor_photo"]["tmp_name"], $destination)) {
                        flash("error", "Could not save doctor photo.");
                        redirect("index.php?page=profile");
                    }

                    $doctorPhotoPath = "public/uploads/doctor_photos/" . $fileName;
                }

                $doctorModel->updatePhoto($doctorId, $doctorPhotoPath);
                $_SESSION["user"]["doctor_photo"] = $doctorPhotoPath;
            }
        } 

        $userModel->update(Auth::currentUser()["id"], [
            "name" => $name,
            "phone" => $phone,
            "avatar" => $avatarPath
        ]);

        $_SESSION["user"]["name"] = $name;

        if (Auth::role() !== "doctor") {
            $_SESSION["user"]["avatar"] = $avatarPath;
        }

        flash("success", "Profile updated successfully.");
        redirect("index.php?page=profile");
    }

    public function password()
    {
        if (!Auth::check()) {
            redirect("index.php?page=login");
        }

        require_once __DIR__ . "/../views/profile/password.php";
    }

    public function updatePassword()
    {
        if (!Auth::check()) {
            redirect("index.php?page=login");
        }

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=profile&action=password");
        }

        $currentPassword = $_POST["current_password"] ?? "";
        $newPassword = $_POST["new_password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        if ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {
            flash("error", "All password fields are required.");
            redirect("index.php?page=profile&action=password");
        }

        if ($newPassword !== $confirmPassword) {
            flash("error", "New password and confirmation do not match.");
            redirect("index.php?page=profile&action=password");
        }

        if (strlen($newPassword) < 8) {
            flash("error", "New password must be at least 8 characters.");
            redirect("index.php?page=profile&action=password");
        }

        $userModel = new UserModel();
        $user = $userModel->findById(Auth::currentUser()["id"]);

        if (!$user || !password_verify($currentPassword, $user["password"])) {
            flash("error", "Current password is incorrect.");
            redirect("index.php?page=profile&action=password");
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);

        $userModel->updatePassword(Auth::currentUser()["id"], $newHash);

        flash("success", "Password updated successfully.");
        redirect("index.php?page=profile");
    }
}