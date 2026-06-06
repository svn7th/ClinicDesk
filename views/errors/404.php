<?php
$pageTitle = "404 Not Found";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>404 Not Found</h1>
        </div>
    </section>

    <section class="content">
        <div class="error-page">
            <h2 class="headline text-danger">404</h2>

            <div class="error-content">
                <h3>
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    Page not found.
                </h3>

                <p>
                    The page you requested does not exist.
                    You may return to the dashboard.
                </p>

                <a href="index.php?page=dashboard" class="btn btn-primary">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>