<?php

session_start();

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/core/helpers.php";

$page = $_GET["page"] ?? "login";
$action = $_GET["action"] ?? "index";

if ($page === "login") {
    require_once __DIR__ . "/controllers/AuthController.php";

    $controller = new AuthController();

    if ($action === "login" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->login();
    } else {
        $controller->showLogin();
    }

    exit;
}

if ($page === "logout" && $_SERVER["REQUEST_METHOD"] === "POST") {
    require_once __DIR__ . "/controllers/AuthController.php";

    $controller = new AuthController();
    $controller->logout();
    exit;
}

if ($page === "dashboard") {
    require_once __DIR__ . "/controllers/DashboardController.php";

    $controller = new DashboardController();
    $controller->index();
    exit;
}

if ($page === "users") {
    require_once __DIR__ . "/controllers/UserController.php";

    $controller = new UserController();

    if ($action === "create") {
        $controller->create();
    } elseif ($action === "store" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->store();
    } elseif ($action === "toggle" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->toggle();
    } elseif ($action === "edit") {
        $controller->edit();
    } elseif ($action === "update" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->updateUser();
    } else {
        $controller->index();
    }

    exit;
}

if ($page === "doctors") {
    require_once __DIR__ . "/controllers/DoctorController.php";

    $controller = new DoctorController();

    if ($action === "create") {
        $controller->create();
    } elseif ($action === "store" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->store();
    } elseif ($action === "edit") {
        $controller->edit();
    } elseif ($action === "update" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->updateDoctor();
    } else {
        $controller->index();
    }

    exit;
}

if ($page === "appointments") {
    require_once __DIR__ . "/controllers/AppointmentController.php";

    $controller = new AppointmentController();

    if ($action === "book") {
        $controller->book();
    } elseif ($action === "store" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->store();
    } elseif ($action === "status" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->updateStatus();
    } else {
        $controller->index();
    }

    exit;
}

if ($page === "prescriptions") {
    require_once __DIR__ . "/controllers/PrescriptionController.php";

    $controller = new PrescriptionController();

    if ($action === "create") {
        $controller->create();
    } elseif ($action === "store" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->store();
    } elseif ($action === "download") {
        $controller->download();
    } else {
        $controller->index();
    }

    exit;
}

if ($page === "reports") {
    require_once __DIR__ . "/controllers/ReportController.php";

    $controller = new ReportController();
    $controller->index();
    exit;
}

if ($page === "specializations") {
    require_once __DIR__ . "/controllers/SpecializationController.php";

    $controller = new SpecializationController();

    if ($action === "store" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->store();
    } elseif ($action === "delete" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->delete();
    } else {
        $controller->index();
    }

    exit;
}

if ($page === "profile") {
    require_once __DIR__ . "/controllers/ProfileController.php";

    $controller = new ProfileController();

    if ($action === "update" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->update();
    } elseif ($action === "password") {
        $controller->password();
    } elseif ($action === "updatePassword" && $_SERVER["REQUEST_METHOD"] === "POST") {
        $controller->updatePassword();
    } else {
        $controller->edit();
    }

    exit;
}


if ($page === "error403") {
    if (!isset($_SESSION["user"])) {
        header("Location: index.php?page=login");
        exit;
    }

    require_once __DIR__ . "/views/errors/403.php";
    exit;
}

if (!isset($_SESSION["user"])) {
    header("Location: index.php?page=login");
    exit;
}

require_once __DIR__ . "/views/errors/404.php";
exit;