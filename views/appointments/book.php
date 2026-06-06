<?php
$pageTitle = "Book Appointment";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Book Appointment</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">New Appointment</h3>
                </div>

                <form method="POST" action="index.php?page=appointments&action=store">
                    <div class="card-body">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="form-group">
                            <label>Doctor</label>
                            <select name="doctor_id" class="form-control" required>
                                <option value="">Choose doctor</option>

                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= sanitize($doctor["id"]) ?>">
                                        Dr. <?= sanitize($doctor["name"]) ?>
                                        - <?= sanitize($doctor["specialization_name"]) ?>
                                        - Available: <?= sanitize($doctor["available_days"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="appt_date" min="<?= date("Y-m-d") ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Time</label>
                            <select name="appt_time" class="form-control" required>
                                <option value="">Choose time</option>

                                <?php
                                $slots = [
                                    "09:00", "09:30", "10:00", "10:30",
                                    "11:00", "11:30", "12:00", "12:30",
                                    "13:00", "13:30", "14:00", "14:30",
                                    "15:00", "15:30", "16:00"
                                ];
                                ?>

                                <?php foreach ($slots as $slot): ?>
                                    <option value="<?= $slot ?>"><?= $slot ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Reason</label>
                            <input type="text" name="reason" maxlength="255" class="form-control">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                        <a href="index.php?page=appointments" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>