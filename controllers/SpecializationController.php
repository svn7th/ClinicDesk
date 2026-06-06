<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/CSRF.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/SpecializationModel.php";

class SpecializationController
{
    public function index()
    {
        Auth::requireRole("admin");

        $specializationModel = new SpecializationModel();
        $specializations = $specializationModel->getAll();

        require_once __DIR__ . "/../views/specializations/index.php";
    }

    public function store()
    {
        Auth::requireRole("admin");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=specializations");
        }

        $name = trim($_POST["name"] ?? "");

        if ($name === "") {
            flash("error", "Specialization name is required.");
            redirect("index.php?page=specializations");
        }

        $specializationModel = new SpecializationModel();

        if ($specializationModel->findByName($name)) {
            flash("error", "Specialization already exists.");
            redirect("index.php?page=specializations");
        }

        $specializationModel->create($name);

        flash("success", "Specialization created successfully.");
        redirect("index.php?page=specializations");
    }

    public function delete()
    {
        Auth::requireRole("admin");

        if (!CSRF::validateToken($_POST["csrf_token"] ?? "")) {
            flash("error", "Invalid request. Please try again.");
            redirect("index.php?page=specializations");
        }

        $id = (int)($_POST["id"] ?? 0);

        if ($id <= 0) {
            flash("error", "Invalid specialization.");
            redirect("index.php?page=specializations");
        }

        $specializationModel = new SpecializationModel();

        if (!$specializationModel->findById($id)) {
            flash("error", "Specialization not found.");
            redirect("index.php?page=specializations");
        }

        if (!$specializationModel->isSafeToDelete($id)) {
            flash("error", "Cannot delete specialization because doctors are using it.");
            redirect("index.php?page=specializations");
        }

        $specializationModel->delete($id);

        flash("success", "Specialization deleted successfully.");
        redirect("index.php?page=specializations");
    }
}