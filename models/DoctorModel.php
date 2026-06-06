<?php

require_once __DIR__ . "/BaseModel.php";

class DoctorModel extends BaseModel
{
    public function findByUserId(int $userId)
    {
        $result = $this->execute(
            "SELECT doctors.*, users.name, users.email, specializations.name AS specialization_name
             FROM doctors
             JOIN users ON doctors.user_id = users.id
             JOIN specializations ON doctors.specialization_id = specializations.id
             WHERE doctors.user_id = ?
             LIMIT 1",
            "i",
            [$userId]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function getAll()
    {
        $result = $this->execute(
            "SELECT doctors.*, users.name, users.email, specializations.name AS specialization_name
             FROM doctors
             JOIN users ON doctors.user_id = users.id
             JOIN specializations ON doctors.specialization_id = specializations.id
             ORDER BY users.name ASC"
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create(array $data)
    {
        $this->execute(
            "INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days)
             VALUES (?, ?, ?, ?, ?)",
            "iisds",
            [
                $data["user_id"],
                $data["specialization_id"],
                $data["bio"] ?? null,
                $data["consultation_fee"],
                $data["available_days"]
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $doctorId, array $data)
    {
        return $this->execute(
            "UPDATE doctors
            SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ?, photo = ?
            WHERE id = ?",
            "isdssi",
            [
                $data["specialization_id"],
                $data["bio"] ?? null,
                $data["consultation_fee"],
                $data["available_days"],
                $data["photo"] ?? null,
                $doctorId
            ]
        );
    }

    public function getAvailableDays(int $doctorId)
    {
        $result = $this->execute(
            "SELECT available_days FROM doctors WHERE id = ? LIMIT 1",
            "i",
            [$doctorId]
        );

        $doctor = $result->fetch_assoc();

        if (!$doctor) {
            return [];
        }

        return explode(",", $doctor["available_days"]);
    }

    public function findById(int $id)
    {
        $result = $this->execute(
            "SELECT doctors.*, users.name, users.email, specializations.name AS specialization_name
            FROM doctors
            JOIN users ON doctors.user_id = users.id
            JOIN specializations ON doctors.specialization_id = specializations.id
            WHERE doctors.id = ?
            LIMIT 1",
            "i",
            [$id]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function findDoctorIdByUserId(int $userId)
    {
        $result = $this->execute(
            "SELECT id FROM doctors WHERE user_id = ? LIMIT 1",
            "i",
            [$userId]
        );

        $row = $result->fetch_assoc();

        return $row ? (int)$row["id"] : null;
    }

    public function updatePhoto(int $doctorId, ?string $photoPath)
    {
        return $this->execute(
            "UPDATE doctors SET photo = ? WHERE id = ?",
            "si",
            [$photoPath, $doctorId]
        );
    }
}