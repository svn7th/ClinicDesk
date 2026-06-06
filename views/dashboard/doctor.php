<?php
$pageTitle = "Doctor Dashboard";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Doctor Dashboard</h1>
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
                            <h3><?= sanitize($doctorStats["total_this_month"] ?? 0) ?></h3>
                            <p>Appointments This Month</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= sanitize($doctorStats["pending"] ?? 0) ?></h3>
                            <p>Pending Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= sanitize($doctorStats["completed"] ?? 0) ?></h3>
                            <p>Completed Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Today&apos;s Appointments</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Patient</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>

                        <?php if (empty($todayAppointments)): ?>
                            <tr>
                                <td colspan="4">No appointments today.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($todayAppointments as $appointment): ?>
                            <tr>
                                <td><?= sanitize($appointment["patient_name"]) ?></td>
                                <td><?= sanitize($appointment["appt_time"]) ?></td>
                                <td><?= sanitize($appointment["status"]) ?></td>
                                <td><?= sanitize($appointment["reason"] ?? "") ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Upcoming Appointments</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>

                        <?php if (empty($upcomingAppointments)): ?>
                            <tr>
                                <td colspan="4">No upcoming appointments.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($upcomingAppointments as $appointment): ?>
                            <tr>
                                <td><?= sanitize($appointment["patient_name"]) ?></td>
                                <td><?= sanitize($appointment["appt_date"]) ?></td>
                                <td><?= sanitize($appointment["appt_time"]) ?></td>
                                <td><?= sanitize($appointment["status"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <a href="index.php?page=appointments" class="btn btn-primary">
                View My Schedule
            </a>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>