<?php

require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class AuthController
{
    public function showLogin()
    {
        if (Auth::check()) {
            redirect("index.php?page=dashboard");
        }

        require_once __DIR__ . "/../views/auth/login.php";
    }

    public function login()
    {
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=login");
        }

        $email = filter_var($_POST["email"] ?? "", FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"] ?? "";

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            flash("error", "Invalid credentials.");
            redirect("index.php?page=login");
        }

        if ((int)$user["is_active"] !== 1) {
            flash("error", "Account suspended. Contact admin.");
            redirect("index.php?page=login");
        }

        if (!password_verify($password, $user["password"])) {
            flash("error", "Invalid credentials.");
            redirect("index.php?page=login");
        }

        Auth::login($user);

        if ($user["role"] === "doctor") {
            $doctorModel = new DoctorModel();
            $doctor = $doctorModel->findByUserId((int)$user["id"]);

            $_SESSION["user"]["doctor_photo"] = $doctor["photo"] ?? null;
        }

        redirect("index.php?page=dashboard");
    }

    public function logout()
    {
        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid logout request.");
            redirect("index.php?page=dashboard");
        }

        Auth::logout();
        redirect("index.php?page=login");
    }
}