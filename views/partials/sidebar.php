<?php require_once __DIR__ . "/../../core/Auth.php"; ?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php?page=dashboard" class="brand-link">
        <span class="brand-text font-weight-light ml-2">ClinicDesk</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <?php
            $sidebarImage = null;

            if (Auth::role() === "doctor") {
                $sidebarImage = $_SESSION["user"]["doctor_photo"] ?? null;
            } else {
                $sidebarImage = $_SESSION["user"]["avatar"] ?? null;
            }
            ?>

            <?php if (!empty($sidebarImage)): ?>
                <div class="image">
                    <img src="<?= htmlspecialchars($sidebarImage, ENT_QUOTES, "UTF-8") ?>" class="img-circle elevation-2" alt="User Image">
                </div>
            <?php endif; ?>

            <div class="info">
                <a href="index.php?page=dashboard" class="d-block">
                    <?= htmlspecialchars($_SESSION["user"]["name"] ?? "User", ENT_QUOTES, "UTF-8") ?>
                    <br>
                    <small><?= htmlspecialchars(Auth::role(), ENT_QUOTES, "UTF-8") ?></small>
                </a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                <li class="nav-item">
                    <a href="index.php?page=dashboard" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if (Auth::role() === "admin"): ?>
                    <li class="nav-item">
                        <a href="index.php?page=users" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Users</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=doctors" class="nav-link">
                            <i class="nav-icon fas fa-user-md"></i>
                            <p>Doctors</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=specializations" class="nav-link">
                            <i class="nav-icon fas fa-stethoscope"></i>
                            <p>Specializations</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=appointments" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Appointments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=reports" class="nav-link">
                            <i class="nav-icon fas fa-file-csv"></i>
                            <p>Reports</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (Auth::role() === "doctor"): ?>
                    <li class="nav-item">
                        <a href="index.php?page=appointments" class="nav-link">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>My Schedule</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (Auth::role() === "patient"): ?>
                    <li class="nav-item">
                        <a href="index.php?page=appointments&action=book" class="nav-link">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Book Appointment</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=appointments" class="nav-link">
                            <i class="nav-icon fas fa-calendar"></i>
                            <p>My Appointments</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="index.php?page=prescriptions" class="nav-link">
                            <i class="nav-icon fas fa-prescription"></i>
                            <p>My Prescriptions</p>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="index.php?page=profile" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>My Profile</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>