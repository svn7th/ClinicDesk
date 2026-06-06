<?php
$pageTitle = "403 Forbidden";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>403 Forbidden</h1>
        </div>
    </section>

    <section class="content">
        <div class="error-page">
            <h2 class="headline text-warning">403</h2>

            <div class="error-content">
                <h3>
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    Access denied.
                </h3>

                <p>
                    You do not have permission to access this page.
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