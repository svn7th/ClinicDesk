<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../core/Paginator.php";
require_once __DIR__ . "/../models/UserModel.php";

class UserController
{
    public function index()
    {
        Auth::requireRole("admin");

        $userModel = new UserModel();

        $page = isset($_GET["p"]) ? (int) $_GET["p"] : 1;
        $role = $_GET["role"] ?? "";

        $totalUsers = $userModel->countAll($role);
        $paginator = new Paginator($totalUsers, ITEMS_PER_PAGE, $page);

        $users = $userModel->getAllPaginated($page, $role);

        require_once __DIR__ . "/../views/users/index.php";
    }

    public function create()
    {
        Auth::requireRole("admin");

        require_once __DIR__ . "/../views/users/create.php";
    }

    public function store()
    {
        Auth::requireRole("admin");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=users&action=create");
        }

        $name = trim($_POST["name"] ?? "");
        $email = filter_var($_POST["email"] ?? "", FILTER_SANITIZE_EMAIL);
        $password = $_POST["password"] ?? "";
        $role = $_POST["role"] ?? "patient";
        $phone = trim($_POST["phone"] ?? "");

        if ($name === "" || $email === "" || $password === "") {
            flash("error", "Name, email, and password are required.");
            redirect("index.php?page=users&action=create");
        }

        if (!in_array($role, ["admin", "doctor", "patient"])) {
            flash("error", "Invalid role selected.");
            redirect("index.php?page=users&action=create");
        }

        $userModel = new UserModel();

        if ($userModel->findByEmail($email)) {
            flash("error", "Email already exists.");
            redirect("index.php?page=users&action=create");
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $userModel->create([
            "name" => $name,
            "email" => $email,
            "password" => $hash,
            "role" => $role,
            "phone" => $phone
        ]);

        flash("success", "User created successfully.");
        redirect("index.php?page=users");
    }

    public function toggle()
    {
        Auth::requireRole("admin");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=users");
        }

        $id = (int) ($_POST["id"] ?? 0);

        if ($id <= 0) {
            flash("error", "Invalid user.");
            redirect("index.php?page=users");
        }

        $currentUser = Auth::currentUser();

        if ((int)$currentUser["id"] === $id) {
            flash("error", "You cannot deactivate your own account.");
            redirect("index.php?page=users");
        }

        $userModel = new UserModel();
        $userModel->toggleActive($id);

        flash("success", "User status updated successfully.");
        redirect("index.php?page=users");
    }

    public function edit()
    {
        Auth::requireRole("admin");

        $id = (int) ($_GET["id"] ?? 0);

        if ($id <= 0) {
            flash("error", "Invalid user.");
            redirect("index.php?page=users");
        }

        $userModel = new UserModel();
        $user = $userModel->findById($id);

        if (!$user) {
            flash("error", "User not found.");
            redirect("index.php?page=users");
        }

        require_once __DIR__ . "/../views/users/edit.php";
    }

    public function updateUser()
    {
        Auth::requireRole("admin");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=users");
        }

        $id = (int) ($_POST["id"] ?? 0);
        $name = trim($_POST["name"] ?? "");
        $phone = trim($_POST["phone"] ?? "");

        if ($id <= 0 || $name === "") {
            flash("error", "Name is required.");
            redirect("index.php?page=users");
        }

        $userModel = new UserModel();

        if (!$userModel->findById($id)) {
            flash("error", "User not found.");
            redirect("index.php?page=users");
        }

        $userModel->update($id, [
            "name" => $name,
            "phone" => $phone
        ]);

        flash("success", "User updated successfully.");
        redirect("index.php?page=users");
    }
}