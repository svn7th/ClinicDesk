<?php
$pageTitle = "Add Prescription";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Add Prescription</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Appointment Details</h3>
                </div>

                <div class="card-body">
                    <p>
                        <strong>Patient:</strong> <?= sanitize($appointment["patient_name"]) ?><br>
                        <strong>Doctor:</strong> <?= sanitize($appointment["doctor_name"]) ?><br>
                        <strong>Date:</strong> <?= sanitize($appointment["appt_date"]) ?><br>
                        <strong>Time:</strong> <?= sanitize($appointment["appt_time"]) ?>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Prescription Form</h3>
                </div>

                <form method="POST" action="index.php?page=prescriptions&action=store" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="appointment_id" value="<?= sanitize($appointment["id"]) ?>">

                        <div class="form-group">
                            <label>Diagnosis</label>
                            <textarea name="diagnosis" rows="4" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Medications</label>
                            <textarea name="medications" rows="4" class="form-control" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" rows="4" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Prescription PDF Optional</label>
                            <input type="file" name="prescription_file" accept="application/pdf" class="form-control">
                            <small class="text-muted">PDF only, max 3 MB.</small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Save Prescription</button>
                        <a href="index.php?page=appointments" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>