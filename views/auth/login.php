<?php require_once __DIR__ . "/../../core/CSRF.php"; ?>
<?php require_once __DIR__ . "/../../core/helpers.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - ClinicDesk</title>

    <link rel="stylesheet" href="public/assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="public/assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="public/assets/adminlte/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">

<div class="login-box">
    <div class="login-logo">
        <b>Clinic</b>Desk
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <?php if (!empty($_SESSION["flash"])): ?>
                <div class="alert alert-danger">
                    <?= sanitize($_SESSION["flash"]["message"]) ?>
                </div>
                <?php unset($_SESSION["flash"]); ?>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login&action=login">
                <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">

                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Sign In
                </button>
            </form>

            <hr>

            <p class="mb-0 text-muted">
                ClinicDesk private system
            </p>
        </div>
    </div>
</div>

<script src="public/assets/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="public/assets/adminlte/dist/js/adminlte.min.js"></script>

</body>
</html>