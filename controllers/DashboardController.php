<?php

require_once __DIR__ . "/../core/Auth.php";
require_once __DIR__ . "/../core/helpers.php";
require_once __DIR__ . "/../models/DashboardModel.php";
require_once __DIR__ . "/../models/DoctorModel.php";

class DashboardController
{
    public function index()
    {
        if (!Auth::check()) {
            redirect("index.php?page=login");
        }

        $role = Auth::role();

        if ($role === "admin") {
            $dashboardModel = new DashboardModel();

            $userCounts = $dashboardModel->getUserCountsByRole();
            $todayAppointments = $dashboardModel->getTodayAppointmentsCount();
            $statusCounts = $dashboardModel->getAppointmentsByStatus();
            $recentAppointments = $dashboardModel->getRecentAppointments();

            require_once __DIR__ . "/../views/dashboard/admin.php";
            return;
        }

        if ($role === "doctor") {
            $dashboardModel = new DashboardModel();
            $doctorModel = new DoctorModel();

            $doctorId = $doctorModel->findDoctorIdByUserId(Auth::currentUser()["id"]);

            if (!$doctorId) {
                flash("error", "Doctor profile not found.");
                redirect("index.php?page=login");
            }

            $doctorStats = $dashboardModel->getDoctorDashboardStats($doctorId);
            $todayAppointments = $dashboardModel->getDoctorTodayAppointments($doctorId);
            $upcomingAppointments = $dashboardModel->getDoctorUpcomingAppointments($doctorId);

            require_once __DIR__ . "/../views/dashboard/doctor.php";
            return;
        }

        if ($role === "patient") {
            $dashboardModel = new DashboardModel();

            $patientId = Auth::currentUser()["id"];

            $patientStats = $dashboardModel->getPatientDashboardStats($patientId);
            $prescriptionCount = $dashboardModel->getPatientPrescriptionCount($patientId);
            $nextAppointment = $dashboardModel->getPatientNextAppointment($patientId);

            require_once __DIR__ . "/../views/dashboard/patient.php";
            return;
        }

        redirect("index.php?page=error403");
    }
}