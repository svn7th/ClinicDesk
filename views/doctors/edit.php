<?php
$pageTitle = "Edit Doctor";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit Doctor</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Doctor Details</h3>
                </div>

                <form method="POST" action="index.php?page=doctors&action=update" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="id" value="<?= sanitize($doctor["id"]) ?>">

                        <div class="form-group">
                            <label>Doctor</label>
                            <input type="text" value="<?= sanitize($doctor["name"]) ?> - <?= sanitize($doctor["email"]) ?>" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label>Specialization</label>
                            <select name="specialization_id" class="form-control" required>
                                <?php foreach ($specializations as $specialization): ?>
                                    <option value="<?= sanitize($specialization["id"]) ?>"
                                        <?= (int)$doctor["specialization_id"] === (int)$specialization["id"] ? "selected" : "" ?>>
                                        <?= sanitize($specialization["name"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Consultation Fee</label>
                            <input type="number" step="0.01" name="consultation_fee" value="<?= sanitize($doctor["consultation_fee"]) ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Available Days</label><br>

                            <?php foreach (["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"] as $day): ?>
                                <label class="mr-3">
                                    <input type="checkbox" name="available_days[]" value="<?= $day ?>"
                                        <?= in_array($day, $selectedDays) ? "checked" : "" ?>>
                                    <?= $day ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-group">
                            <label>Doctor Photo</label>
                            <input type="file" name="photo" accept="image/jpeg,image/png" class="form-control">
                            <small class="text-muted">JPG/PNG only, max 1 MB. Uploading a new photo will replace the old one.</small>

                            <?php if (!empty($doctor["photo"])): ?>
                                <br>
                                <img src="<?= sanitize($doctor["photo"]) ?>" alt="Doctor Photo" width="120" class="mt-2">

                                <div class="form-check mt-2">
                                    <input type="checkbox" name="remove_photo" value="1" class="form-check-input" id="remove_photo">
                                    <label class="form-check-label" for="remove_photo">
                                        Remove current photo
                                    </label>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>Bio</label>
                            <textarea name="bio" rows="4" class="form-control"><?= sanitize($doctor["bio"] ?? "") ?></textarea>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Doctor</button>
                        <a href="index.php?page=doctors" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>