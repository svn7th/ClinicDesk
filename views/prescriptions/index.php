<?php
$pageTitle = "My Prescriptions";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>My Prescriptions</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Prescriptions</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>ID</th>
                            <th>Doctor</th>
                            <th>Specialization</th>
                            <th>Date</th>
                            <th>Diagnosis</th>
                            <th>Medications</th>
                            <th>Notes</th>
                            <th>PDF</th>
                        </tr>

                        <?php if (empty($prescriptions)): ?>
                            <tr>
                                <td colspan="8">No prescriptions found.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($prescriptions as $prescription): ?>
                            <tr>
                                <td><?= sanitize($prescription["id"]) ?></td>
                                <td><?= sanitize($prescription["doctor_name"]) ?></td>
                                <td><?= sanitize($prescription["specialization_name"]) ?></td>
                                <td><?= sanitize($prescription["appt_date"]) ?></td>
                                <td><?= sanitize($prescription["diagnosis"]) ?></td>
                                <td><?= sanitize($prescription["medications"]) ?></td>
                                <td><?= sanitize($prescription["notes"] ?? "") ?></td>
                                <td>
                                    <?php if (!empty($prescription["file_path"])): ?>
                                        <a href="index.php?page=prescriptions&action=download&id=<?= sanitize($prescription["id"]) ?>" class="btn btn-sm btn-primary">
                                            Download PDF
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">No file</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>