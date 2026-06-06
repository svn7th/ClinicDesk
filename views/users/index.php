<?php
$pageTitle = "Users";
require_once __DIR__ . "/../partials/header.php";
require_once __DIR__ . "/../partials/navbar.php";
require_once __DIR__ . "/../partials/sidebar.php";
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Users</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <?php require_once __DIR__ . "/../partials/alerts.php"; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Management</h3>

                    <div class="card-tools">
                        <a href="index.php?page=users&action=create" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create New User
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    <form method="GET" action="index.php" class="form-inline mb-3">
                        <input type="hidden" name="page" value="users">

                        <label class="mr-2">Filter by role:</label>

                        <select name="role" class="form-control mr-2">
                            <option value="">All</option>
                            <option value="admin" <?= $role === "admin" ? "selected" : "" ?>>Admin</option>
                            <option value="doctor" <?= $role === "doctor" ? "selected" : "" ?>>Doctor</option>
                            <option value="patient" <?= $role === "patient" ? "selected" : "" ?>>Patient</option>
                        </select>

                        <button type="submit" class="btn btn-secondary">
                            Filter
                        </button>
                    </form>

                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Active</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>

                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= sanitize($user["id"]) ?></td>
                                <td><?= sanitize($user["name"]) ?></td>
                                <td><?= sanitize($user["email"]) ?></td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= sanitize($user["role"]) ?>
                                    </span>
                                </td>
                                <td><?= sanitize($user["phone"] ?? "") ?></td>
                                <td>
                                    <?php if ($user["is_active"]): ?>
                                        <span class="badge badge-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= sanitize($user["created_at"]) ?></td>
                                <td>
                                    <a href="index.php?page=users&action=edit&id=<?= sanitize($user["id"]) ?>" class="btn btn-sm btn-info">
                                        Edit
                                    </a>

                                    <?php if ((int)$user["id"] !== (int)$_SESSION["user"]["id"]): ?>
                                        <form method="POST" action="index.php?page=users&action=toggle" style="display:inline-block;">
                                            <input type="hidden" name="csrf_token" value="<?= CSRF::generateToken() ?>">
                                            <input type="hidden" name="id" value="<?= sanitize($user["id"]) ?>">

                                            <button type="submit" class="btn btn-sm <?= $user["is_active"] ? "btn-danger" : "btn-success" ?>">
                                                <?= $user["is_active"] ? "Deactivate" : "Activate" ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Current user</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                    <div class="mt-3">
                        <?php if ($paginator->hasPrev()): ?>
                            <a class="btn btn-sm btn-secondary" href="index.php?page=users&p=<?= $paginator->currentPage() - 1 ?>&role=<?= urlencode($role) ?>">
                                Previous
                            </a>
                        <?php endif; ?>

                        <span class="mx-2">
                            Page <?= $paginator->currentPage() ?> of <?= max(1, $paginator->totalPages()) ?>
                        </span>

                        <?php if ($paginator->hasNext()): ?>
                            <a class="btn btn-sm btn-secondary" href="index.php?page=users&p=<?= $paginator->currentPage() + 1 ?>&role=<?= urlencode($role) ?>">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>