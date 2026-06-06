<?php
$pageTitle = "Appointments";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Appointments</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Appointments List</h3>

                    <?php if (Auth::role() === "patient"): ?>
                        <div class="card-tools">
                            <a href="index.php?page=appointments&action=book" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Book Appointment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body">

                    <form method="GET" action="index.php" class="form-inline mb-3">
                        <input type="hidden" name="page" value="appointments">

                        <label class="mr-2">Status:</label>
                        <select name="status" class="form-control mr-2">
                            <option value="">All</option>
                            <option value="pending" <?= ($_GET["status"] ?? "") === "pending" ? "selected" : "" ?>>Pending</option>
                            <option value="confirmed" <?= ($_GET["status"] ?? "") === "confirmed" ? "selected" : "" ?>>Confirmed</option>
                            <option value="completed" <?= ($_GET["status"] ?? "") === "completed" ? "selected" : "" ?>>Completed</option>
                            <option value="cancelled" <?= ($_GET["status"] ?? "") === "cancelled" ? "selected" : "" ?>>Cancelled</option>
                        </select>

                        <label class="mr-2">Start Date:</label>
                        <input type="date" name="start_date" value="<?= sanitize($_GET["start_date"] ?? "") ?>" class="form-control mr-2">

                        <label class="mr-2">End Date:</label>
                        <input type="date" name="end_date" value="<?= sanitize($_GET["end_date"] ?? "") ?>" class="form-control mr-2">


                        <?php if (Auth::role() === "admin"): ?>
                            <label class="mr-2">Doctor:</label>
                            <select name="doctor_id" class="form-control mr-2">
                                <option value="">All doctors</option>

                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= sanitize($doctor["id"]) ?>"
                                        <?= ($_GET["doctor_id"] ?? "") == $doctor["id"] ? "selected" : "" ?>>
                                        Dr. <?= sanitize($doctor["name"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <label class="mr-2">Patient:</label>
                            <input type="text"
                                name="patient_name"
                                value="<?= sanitize($_GET["patient_name"] ?? "") ?>"
                                class="form-control mr-2"
                                placeholder="Patient name">
                        <?php endif; ?>


                        <button type="submit" class="btn btn-secondary mr-2">
                            Filter
                        </button>

                        <a href="index.php?page=appointments" class="btn btn-outline-secondary">
                            Clear
                        </a>
                    </form>

                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Action</th>
                        </tr>

                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="9">No appointments found.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= sanitize($appointment["id"]) ?></td>
                                <td><?= sanitize($appointment["patient_name"]) ?></td>
                                <td><?= sanitize($appointment["doctor_name"]) ?></td>
                                <td><?= sanitize($appointment["specialization_name"]) ?></td>
                                <td><?= sanitize($appointment["appt_date"]) ?></td>
                                <td><?= sanitize($appointment["appt_time"]) ?></td>
                                <td>
                                    <?php
                                    $badge = "secondary";

                                    if ($appointment["status"] === "pending") {
                                        $badge = "warning";
                                    } elseif ($appointment["status"] === "confirmed") {
                                        $badge = "info";
                                    } elseif ($appointment["status"] === "completed") {
                                        $badge = "success";
                                    } elseif ($appointment["status"] === "cancelled") {
                                        $badge = "danger";
                                    }
                                    ?>

                                    <span class="badge badge-<?= $badge ?>">
                                        <?= sanitize($appointment["status"]) ?>
                                    </span>
                                </td>
                                <td><?= sanitize($appointment["reason"] ?? "") ?></td>

                                <td>
                                    <?php if (Auth::role() === "patient" && $appointment["status"] === "pending"): ?>
                                        <form method="POST" action="index.php?page=appointments&action=status" style="display:inline-block;">
                                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                            <input type="hidden" name="id" value="<?= sanitize($appointment["id"]) ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (in_array(Auth::role(), ["doctor", "admin"])): ?>

                                        <?php if ($appointment["status"] === "pending"): ?>
                                            <form method="POST" action="index.php?page=appointments&action=status" style="display:inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                <input type="hidden" name="id" value="<?= sanitize($appointment["id"]) ?>">
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-sm btn-info">Confirm</button>
                                            </form>

                                            <form method="POST" action="index.php?page=appointments&action=status" style="display:inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                <input type="hidden" name="id" value="<?= sanitize($appointment["id"]) ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($appointment["status"] === "confirmed"): ?>
                                            <form method="POST" action="index.php?page=appointments&action=status" style="display:inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                <input type="hidden" name="id" value="<?= sanitize($appointment["id"]) ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                            </form>

                                            <form method="POST" action="index.php?page=appointments&action=status" style="display:inline-block;">
                                                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                                <input type="hidden" name="id" value="<?= sanitize($appointment["id"]) ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($appointment["status"] === "completed"): ?>
                                            <a href="index.php?page=prescriptions&action=create&appointment_id=<?= sanitize($appointment["id"]) ?>" class="btn btn-sm btn-primary">
                                                Add Prescription
                                            </a>
                                        <?php endif; ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <div class="mt-3">
                        <?php
                        $queryString = http_build_query([
                            "page" => "appointments",
                            "status" => $_GET["status"] ?? "",
                            "start_date" => $_GET["start_date"] ?? "",
                            "end_date" => $_GET["end_date"] ?? "",
                            "doctor_id" => $_GET["doctor_id"] ?? "",
                            "patient_name" => $_GET["patient_name"] ?? ""
                        ]);
                        ?>

                        <?php if ($paginator->hasPrev()): ?>
                            <a class="btn btn-sm btn-secondary" href="index.php?<?= $queryString ?>&p=<?= $paginator->currentPage() - 1 ?>">
                                Previous
                            </a>
                        <?php endif; ?>

                        <span class="mx-2">
                            Page <?= $paginator->currentPage() ?> of <?= max(1, $paginator->totalPages()) ?>
                        </span>

                        <?php if ($paginator->hasNext()): ?>
                            <a class="btn btn-sm btn-secondary" href="index.php?<?= $queryString ?>&p=<?= $paginator->currentPage() + 1 ?>">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>