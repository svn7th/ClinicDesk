<?php
$pageTitle = "Create Doctor";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Create Doctor Record</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Doctor Details</h3>
                </div>

                <form method="POST" action="index.php?page=doctors&action=store">
                    <div class="card-body">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="form-group">
                            <label>Doctor User</label>
                            <select name="user_id" class="form-control" required>
                                <option value="">Choose doctor user</option>
                                <?php foreach ($doctorUsers as $doctorUser): ?>
                                    <option value="<?= sanitize($doctorUser["id"]) ?>">
                                        <?= sanitize($doctorUser["name"]) ?> - <?= sanitize($doctorUser["email"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Specialization</label>
                            <select name="specialization_id" class="form-control" required>
                                <option value="">Choose specialization</option>
                                <?php foreach ($specializations as $specialization): ?>
                                    <option value="<?= sanitize($specialization["id"]) ?>">
                                        <?= sanitize($specialization["name"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Consultation Fee</label>
                            <input type="number" step="0.01" name="consultation_fee" value="0.00" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label><br>

                            <?php foreach (["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"] as $day): ?>
                                <label class="mr-3">
                                    <input type="checkbox" name="available_days[]" value="<?= $day ?>">
                                    <?= $day ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" rows="4" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Doctor Record</button>
                        <a href="index.php?page=doctors" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>