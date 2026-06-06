<?php
$pageTitle = "Reports";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Appointment Reports</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report Filters</h3>
                </div>

                <form method="GET" action="index.php">
                    <div class="card-body">
                        <input type="hidden" name="page" value="reports">

                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="start_date" value="<?= sanitize($startDate) ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" value="<?= sanitize($endDate) ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Doctor Optional</label>
                            <select name="doctor_id" class="form-control">
                                <option value="">All doctors</option>

                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= sanitize($doctor["id"]) ?>"
                                        <?= (string)$doctorId === (string)$doctor["id"] ? "selected" : "" ?>>
                                        Dr. <?= sanitize($doctor["name"]) ?> - <?= sanitize($doctor["specialization_name"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status Optional</label>
                            <select name="status" class="form-control">
                                <option value="">All statuses</option>
                                <option value="pending" <?= $status === "pending" ? "selected" : "" ?>>Pending</option>
                                <option value="confirmed" <?= $status === "confirmed" ? "selected" : "" ?>>Confirmed</option>
                                <option value="completed" <?= $status === "completed" ? "selected" : "" ?>>Completed</option>
                                <option value="cancelled" <?= $status === "cancelled" ? "selected" : "" ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Generate Report</button>

                        <?php if ($startDate !== "" && $endDate !== ""): ?>
                            <button type="submit" name="export" value="csv" class="btn btn-success">
                                Export CSV
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <?php if ($startDate !== "" && $endDate !== ""): ?>

                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3><?= count($rows) ?></h3>
                                <p>Total Shown</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-list"></i>
                            </div>
                        </div>
                    </div>

                    <?php foreach ($summary as $statusName => $total): ?>
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?= sanitize($total) ?></h3>
                                    <p><?= ucfirst(sanitize($statusName)) ?></p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Report Results</h3>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <tr>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Specialization</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>

                            <?php if (empty($rows)): ?>
                                <tr>
                                    <td colspan="7">No appointments found for this filter.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?= sanitize($row["patient_name"]) ?></td>
                                    <td><?= sanitize($row["doctor_name"]) ?></td>
                                    <td><?= sanitize($row["specialization_name"]) ?></td>
                                    <td><?= sanitize($row["appt_date"]) ?></td>
                                    <td><?= sanitize($row["appt_time"]) ?></td>
                                    <td><?= sanitize($row["status"]) ?></td>
                                    <td><?= sanitize($row["reason"] ?? "") ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>