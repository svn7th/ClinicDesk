<?php

require_once __DIR__ . "/BaseModel.php";

class PrescriptionModel extends BaseModel
{
    public function findByAppointmentId(int $appointmentId)
    {
        $result = $this->execute(
            "SELECT * FROM prescriptions WHERE appointment_id = ? LIMIT 1",
            "i",
            [$appointmentId]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function create(array $data)
    {
        $this->execute(
            "INSERT INTO prescriptions
            (appointment_id, diagnosis, medications, notes, file_path)
            VALUES (?, ?, ?, ?, ?)",
            "issss",
            [
                $data["appointment_id"],
                $data["diagnosis"],
                $data["medications"],
                $data["notes"] ?? null,
                $data["file_path"] ?? null
            ]
        );

        return $this->db->lastInsertId();
    }

    public function getByPatient(int $patientId)
    {
        $result = $this->execute(
            "SELECT prescriptions.*,
                    appointments.appt_date,
                    appointments.appt_time,
                    doctor_user.name AS doctor_name,
                    specializations.name AS specialization_name
             FROM prescriptions
             JOIN appointments ON prescriptions.appointment_id = appointments.id
             JOIN doctors ON appointments.doctor_id = doctors.id
             JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
             JOIN specializations ON doctors.specialization_id = specializations.id
             WHERE appointments.patient_id = ?
             ORDER BY prescriptions.created_at DESC",
            "i",
            [$patientId]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findByIdWithAppointment(int $id)
    {
        $result = $this->execute(
            "SELECT prescriptions.*,
                    appointments.patient_id,
                    appointments.doctor_id,
                    appointments.appt_date,
                    appointments.appt_time,
                    doctor_user.name AS doctor_name,
                    patient_user.name AS patient_name
            FROM prescriptions
            JOIN appointments ON prescriptions.appointment_id = appointments.id
            JOIN doctors ON appointments.doctor_id = doctors.id
            JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
            JOIN users AS patient_user ON appointments.patient_id = patient_user.id
            WHERE prescriptions.id = ?
            LIMIT 1",
            "i",
            [$id]
        );

        return $result->fetch_assoc() ?: null;
    }
}