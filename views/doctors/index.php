<?php
$pageTitle = "Doctors";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Doctors</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Doctor Management</h3>

                    <div class="card-tools">
                        <a href="index.php?page=doctors&action=create" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create Doctor Record
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Doctor Name</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Fee</th>
                            <th>Available Days</th>
                            <th>Action</th>
                        </tr>

                        <?php if (empty($doctors)): ?>
                            <tr>
                                <td colspan="8">No doctors found yet.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td><?= sanitize($doctor["id"]) ?></td>
                                <td>
                                    <?php if (!empty($doctor["photo"])): ?>
                                        <img src="<?= sanitize($doctor["photo"]) ?>" width="60" alt="Doctor Photo">
                                    <?php else: ?>
                                        <span class="badge badge-secondary">No photo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= sanitize($doctor["name"]) ?></td>
                                <td><?= sanitize($doctor["email"]) ?></td>
                                <td><?= sanitize($doctor["specialization_name"]) ?></td>
                                <td><?= sanitize($doctor["consultation_fee"]) ?></td>
                                <td><?= sanitize($doctor["available_days"]) ?></td>
                                <td>
                                    <a href="index.php?page=doctors&action=edit&id=<?= sanitize($doctor["id"]) ?>" class="btn btn-sm btn-info">
                                        Edit
                                    </a>
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