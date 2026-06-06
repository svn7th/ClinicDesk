<?php
$pageTitle = "Edit User";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Edit User</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit User Details</h3>
                </div>

                <form method="POST" action="index.php?page=users&action=update">
                    <div class="card-body">
                        <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                        <input type="hidden" name="id" value="<?= sanitize($user["id"]) ?>">

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

                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" value="<?= sanitize($user["phone"] ?? "") ?>" class="form-control">
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update User</button>
                        <a href="index.php?page=users" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>