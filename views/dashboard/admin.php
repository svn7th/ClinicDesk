<?php
$pageTitle = "Admin Dashboard";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Admin Dashboard</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="row">
                <?php foreach ($userCounts as $count): ?>
                    <div class="col-md-4">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?= sanitize($count["total"]) ?></h3>
                                <p><?= ucfirst(sanitize($count["role"])) ?> Users</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= sanitize($todayAppointments) ?></h3>
                            <p>Appointments Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Appointments by Status</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>

                        <?php foreach ($statusCounts as $count): ?>
                            <tr>
                                <td><?= sanitize($count["status"]) ?></td>
                                <td><?= sanitize($count["total"]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Appointments</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>

                        <?php if (empty($recentAppointments)): ?>
                            <tr>
                                <td colspan="5">No recent appointments.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($recentAppointments as $appointment): ?>
                            <tr>
                                <td><?= sanitize($appointment["patient_name"]) ?></td>
                                <td><?= sanitize($appointment["doctor_name"]) ?></td>
                                <td><?= sanitize($appointment["appt_date"]) ?></td>
                                <td><?= sanitize($appointment["appt_time"]) ?></td>
                                <td>
                                    <span class="badge badge-secondary">
                                        <?= sanitize($appointment["status"]) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Management</h3>
                </div>

                <div class="card-body">
                    <a href="index.php?page=users" class="btn btn-primary">Manage Users</a>
                    <a href="index.php?page=doctors" class="btn btn-info">Manage Doctors</a>
                    <a href="index.php?page=appointments" class="btn btn-success">Appointments</a>
                    <a href="index.php?page=reports" class="btn btn-secondary">Reports</a>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>