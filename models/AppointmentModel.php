<?php

require_once __DIR__ . "/BaseModel.php";

class AppointmentModel extends BaseModel
{
    public function hasConflict(int $doctorId, string $date, string $time)
    {
        $result = $this->execute(
            "SELECT id FROM appointments
             WHERE doctor_id = ? AND appt_date = ? AND appt_time = ?
             LIMIT 1",
            "iss",
            [$doctorId, $date, $time]
        );

        return $result->num_rows > 0;
    }

    public function book(array $data)
    {
        try {
            $this->execute(
                "INSERT INTO appointments
                (patient_id, doctor_id, appt_date, appt_time, status, reason)
                VALUES (?, ?, ?, ?, 'pending', ?)",
                "iisss",
                [
                    $data["patient_id"],
                    $data["doctor_id"],
                    $data["appt_date"],
                    $data["appt_time"],
                    $data["reason"] ?? null
                ]
            );

            return true;
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }

    private function buildFilters(array $filters, array &$conditions, string &$types, array &$params)
    {
        if (!empty($filters["status"])) {
            $conditions[] = "appointments.status = ?";
            $types .= "s";
            $params[] = $filters["status"];
        }

        if (!empty($filters["start_date"])) {
            $conditions[] = "appointments.appt_date >= ?";
            $types .= "s";
            $params[] = $filters["start_date"];
        }

        if (!empty($filters["end_date"])) {
            $conditions[] = "appointments.appt_date <= ?";
            $types .= "s";
            $params[] = $filters["end_date"];
        }

        if (!empty($filters["doctor_id"])) {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = (int)$filters["doctor_id"];
        }

        if (!empty($filters["patient_name"])) {
            $conditions[] = "users.name LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters["patient_name"] . "%";
        }
    }

    private function selectSql()
    {
        return "
            SELECT appointments.*, 
                   users.name AS patient_name,
                   doctor_user.name AS doctor_name,
                   specializations.name AS specialization_name
            FROM appointments
            JOIN users ON appointments.patient_id = users.id
            JOIN doctors ON appointments.doctor_id = doctors.id
            JOIN users AS doctor_user ON doctors.user_id = doctor_user.id
            JOIN specializations ON doctors.specialization_id = specializations.id
        ";
    }

    public function getByPatient(int $patientId, int $page, array $filters = [])
    {
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $conditions = ["appointments.patient_id = ?"];
        $types = "i";
        $params = [$patientId];

        $this->buildFilters($filters, $conditions, $types, $params);

        $where = implode(" AND ", $conditions);

        $sql = $this->selectSql() . "
            WHERE $where
            ORDER BY appointments.appt_date DESC, appointments.appt_time DESC
            LIMIT ? OFFSET ?
        ";

        $types .= "ii";
        $params[] = $perPage;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByDoctor(int $doctorId, int $page, array $filters = [])
    {
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $conditions = ["appointments.doctor_id = ?"];
        $types = "i";
        $params = [$doctorId];

        $this->buildFilters($filters, $conditions, $types, $params);

        $where = implode(" AND ", $conditions);

        $sql = $this->selectSql() . "
            WHERE $where
            ORDER BY appointments.appt_date DESC, appointments.appt_time DESC
            LIMIT ? OFFSET ?
        ";

        $types .= "ii";
        $params[] = $perPage;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll(int $page, array $filters = [])
    {
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $conditions = [];
        $types = "";
        $params = [];

        $this->buildFilters($filters, $conditions, $types, $params);

        $where = "";

        if (!empty($conditions)) {
            $where = "WHERE " . implode(" AND ", $conditions);
        }

        $sql = $this->selectSql() . "
            $where
            ORDER BY appointments.appt_date DESC, appointments.appt_time DESC
            LIMIT ? OFFSET ?
        ";

        $types .= "ii";
        $params[] = $perPage;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countFiltered(string $scope, int $scopeId = 0, array $filters = [])
    {
        $conditions = [];
        $types = "";
        $params = [];

        if ($scope === "patient") {
            $conditions[] = "appointments.patient_id = ?";
            $types .= "i";
            $params[] = $scopeId;
        }

        if ($scope === "doctor") {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = $scopeId;
        }

        $this->buildFilters($filters, $conditions, $types, $params);

        $where = "";

        if (!empty($conditions)) {
            $where = "WHERE " . implode(" AND ", $conditions);
        }

        $result = $this->execute(
            "SELECT COUNT(*) AS total
            FROM appointments
            JOIN users ON appointments.patient_id = users.id
            $where",
            $types,
            $params
        );

        $row = $result->fetch_assoc();

        return (int)$row["total"];
    }

    public function findById(int $id)
    {
        $result = $this->execute(
            $this->selectSql() . "
             WHERE appointments.id = ?
             LIMIT 1",
            "i",
            [$id]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function updateStatus(int $id, string $status, string $notes = "")
    {
        return $this->execute(
            "UPDATE appointments SET status = ?, doctor_notes = ? WHERE id = ?",
            "ssi",
            [$status, $notes, $id]
        );
    }
}