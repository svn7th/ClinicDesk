<?php
$pageTitle = "Patient Dashboard";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Patient Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <p>Welcome, <?= sanitize($_SESSION["user"]["name"]) ?>.</p>

            <div class="row">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= sanitize($patientStats["active_appointments"] ?? 0) ?></h3>
                            <p>Active Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= sanitize($patientStats["completed_appointments"] ?? 0) ?></h3>
                            <p>Completed Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><?= sanitize($prescriptionCount) ?></h3>
                            <p>Prescriptions Available</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-prescription"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Next Appointment</h3>
                </div>

                <div class="card-body">
                    <?php if ($nextAppointment): ?>
                        <p>
                            <strong>Doctor:</strong> <?= sanitize($nextAppointment["doctor_name"]) ?><br>
                            <strong>Specialization:</strong> <?= sanitize($nextAppointment["specialization_name"]) ?><br>
                            <strong>Date:</strong> <?= sanitize($nextAppointment["appt_date"]) ?><br>
                            <strong>Time:</strong> <?= sanitize($nextAppointment["appt_time"]) ?><br>
                            <strong>Status:</strong> <?= sanitize($nextAppointment["status"]) ?>
                        </p>
                    <?php else: ?>
                        <p>No upcoming appointment.</p>
                    <?php endif; ?>
                </div>
            </div>

            <a href="index.php?page=appointments&action=book" class="btn btn-primary">
                Book Appointment
            </a>

            <a href="index.php?page=appointments" class="btn btn-info">
                My Appointments
            </a>

            <a href="index.php?page=prescriptions" class="btn btn-success">
                My Prescriptions
            </a>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>