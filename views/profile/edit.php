<?php
$pageTitle = "My Profile";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>My Profile</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Profile</h3>
                </div>

                <form method="POST" action="index.php?page=profile&action=update" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="<?= sanitize($user["name"]) ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?= sanitize($user["email"]) ?>" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" value="<?= sanitize($user["role"]) ?>" class="form-control" disabled>
                        </div>

                        <?php if (Auth::role() !== "doctor"): ?>
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <input type="file" name="avatar" accept="image/jpeg,image/png" class="form-control">
                                <small class="text-muted">JPG/PNG only, max 1 MB.</small>

                                <?php if (!empty($user["avatar"])): ?>
                                    <br>
                                    <img src="<?= sanitize($user["avatar"]) ?>" alt="Profile Picture" width="120" class="mt-2">

                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="remove_avatar" value="1" class="form-check-input" id="remove_avatar">
                                        <label class="form-check-label" for="remove_avatar">
                                            Remove current picture
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (Auth::role() === "doctor" && !empty($doctorProfile)): ?>
                            <hr>

                            <div class="form-group">
                                <label>Doctor Professional Photo</label>
                                <input type="file" name="doctor_photo" accept="image/jpeg,image/png" class="form-control">
                                <small class="text-muted">This photo appears in the Doctors list. JPG/PNG only, max 1 MB.</small>

                                <?php if (!empty($doctorProfile["photo"])): ?>
                                    <br>
                                    <img src="<?= sanitize($doctorProfile["photo"]) ?>" alt="Doctor Photo" width="120" class="mt-2">

                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="remove_doctor_photo" value="1" class="form-check-input" id="remove_doctor_photo">
                                        <label class="form-check-label" for="remove_doctor_photo">
                                            Remove current doctor photo
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="<?= sanitize($user["phone"] ?? "") ?>" class="form-control">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <a href="index.php?page=profile&action=password" class="btn btn-warning">Change Password</a>
                        <a href="index.php?page=dashboard" class="btn btn-secondary">Back</a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>