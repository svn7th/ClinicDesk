<?php

require_once __DIR__ . "/BaseModel.php";

class DashboardModel extends BaseModel
{
    public function getUserCountsByRole()
    {
        $result = $this->execute(
            "SELECT role, COUNT(*) AS total
             FROM users
             GROUP BY role"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTodayAppointmentsCount()
    {
        $result = $this->execute(
            "SELECT COUNT(*) AS total
             FROM appointments
             WHERE appt_date = CURDATE()"
        );

        $row = $result->fetch_assoc();

        return (int)$row["total"];
    }

    public function getAppointmentsByStatus()
    {
        $result = $this->execute(
            "SELECT status, COUNT(*) AS total
             FROM appointments
             GROUP BY status"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRecentAppointments()
    {
        $result = $this->execute(
            "SELECT appointments.*,
                    patient.name AS patient_name,
                    doctor_user.name AS doctor_name
             FROM appointments
             JOIN users AS patient ON appointments.patient_id = patient.id
             JOIN doctors ON appointments.doctor_id = doctors.id
             JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
             ORDER BY appointments.created_at DESC
             LIMIT 5"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDoctorDashboardStats(int $doctorId)
{
    $result = $this->execute(
        "SELECT
            SUM(CASE WHEN MONTH(appt_date) = MONTH(CURDATE()) AND YEAR(appt_date) = YEAR(CURDATE()) THEN 1 ELSE 0 END) AS total_this_month,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed
         FROM appointments
         WHERE doctor_id = ?",
        "i",
        [$doctorId]
    );

    return $result->fetch_assoc();
}

    public function getDoctorTodayAppointments(int $doctorId)
    {
        $result = $this->execute(
            "SELECT appointments.*,
                    users.name AS patient_name
            FROM appointments
            JOIN users ON appointments.patient_id = users.id
            WHERE appointments.doctor_id = ?
            AND appointments.appt_date = CURDATE()
            ORDER BY appointments.appt_time ASC",
            "i",
            [$doctorId]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDoctorUpcomingAppointments(int $doctorId)
    {
        $result = $this->execute(
            "SELECT appointments.*,
                    users.name AS patient_name
            FROM appointments
            JOIN users ON appointments.patient_id = users.id
            WHERE appointments.doctor_id = ?
            AND appointments.appt_date >= CURDATE()
            ORDER BY appointments.appt_date ASC, appointments.appt_time ASC
            LIMIT 5",
            "i",
            [$doctorId]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPatientDashboardStats(int $patientId)
    {
        $result = $this->execute(
            "SELECT
                SUM(CASE WHEN status IN ('pending', 'confirmed') THEN 1 ELSE 0 END) AS active_appointments,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_appointments
            FROM appointments
            WHERE patient_id = ?",
            "i",
            [$patientId]
        );

        return $result->fetch_assoc();
    }

    public function getPatientPrescriptionCount(int $patientId)
    {
        $result = $this->execute(
            "SELECT COUNT(*) AS total
            FROM prescriptions
            JOIN appointments ON prescriptions.appointment_id = appointments.id
            WHERE appointments.patient_id = ?",
            "i",
            [$patientId]
        );

        $row = $result->fetch_assoc();

        return (int)$row["total"];
    }

    public function getPatientNextAppointment(int $patientId)
    {
        $result = $this->execute(
            "SELECT appointments.*,
                    doctor_user.name AS doctor_name,
                    specializations.name AS specialization_name
            FROM appointments
            JOIN doctors ON appointments.doctor_id = doctors.id
            JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
            JOIN specializations ON doctors.specialization_id = specializations.id
            WHERE appointments.patient_id = ?
            AND appointments.appt_date >= CURDATE()
            AND appointments.status IN ('pending', 'confirmed')
            ORDER BY appointments.appt_date ASC, appointments.appt_time ASC
            LIMIT 1",
            "i",
            [$patientId]
        );

        return $result->fetch_assoc() ?: null;
    }
}