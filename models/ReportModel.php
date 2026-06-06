<?php

require_once __DIR__ . "/BaseModel.php";

class ReportModel extends BaseModel
{
    public function getAppointmentReport(string $startDate, string $endDate, string $doctorId = "", string $status = "")
    {
        $conditions = [
            "appointments.appt_date BETWEEN ? AND ?"
        ];

        $types = "ss";
        $params = [$startDate, $endDate];

        if ($doctorId !== "") {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = (int)$doctorId;
        }

        if ($status !== "") {
            $conditions[] = "appointments.status = ?";
            $types .= "s";
            $params[] = $status;
        }

        $where = implode(" AND ", $conditions);

        $sql = "
            SELECT appointments.*,
                   patient.name AS patient_name,
                   doctor_user.name AS doctor_name,
                   specializations.name AS specialization_name
            FROM appointments
            JOIN users AS patient ON appointments.patient_id = patient.id
            JOIN doctors ON appointments.doctor_id = doctors.id
            JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
            JOIN specializations ON doctors.specialization_id = specializations.id
            WHERE $where
            ORDER BY appointments.appt_date ASC, appointments.appt_time ASC
        ";

        $result = $this->execute($sql, $types, $params);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getStatusSummary(array $rows)
    {
        $summary = [
            "pending" => 0,
            "confirmed" => 0,
            "completed" => 0,
            "cancelled" => 0
        ];

        foreach ($rows as $row) {
            if (isset($summary[$row["status"]])) {
                $summary[$row["status"]]++;
            }
        }

        return $summary;
    }
}