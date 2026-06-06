<?php

require_once __DIR__ . "/BaseModel.php";

class UserModel extends BaseModel
{
    public function findById(int $id)
    {
        $result = $this->execute(
            "SELECT * FROM users WHERE id = ? LIMIT 1",
            "i",
            [$id]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function findByEmail(string $email)
    {
        $result = $this->execute(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            "s",
            [$email]
        );

        return $result->fetch_assoc() ?: null;
    }

    public function create(array $data)
    {
        $this->execute(
            "INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)",
            "sssss",
            [
                $data["name"],
                $data["email"],
                $data["password"],
                $data["role"],
                $data["phone"] ?? null
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data)
    {
        return $this->execute(
            "UPDATE users SET name = ?, phone = ?, avatar = ? WHERE id = ?",
            "sssi",
            [
                $data["name"],
                $data["phone"] ?? null,
                $data["avatar"] ?? null,
                $id
            ]
        );
    }

    public function updatePassword(int $id, string $newHash)
    {
        return $this->execute(
            "UPDATE users SET password = ? WHERE id = ?",
            "si",
            [$newHash, $id]
        );
    }

    public function getAllPaginated(int $page, string $role = "")
    {
        $perPage = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $perPage;

        if ($role !== "") {
            $result = $this->execute(
                "SELECT * FROM users WHERE role = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
                "sii",
                [$role, $perPage, $offset]
            );
        } else {
            $result = $this->execute(
                "SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?",
                "ii",
                [$perPage, $offset]
            );
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countAll(string $role = "")
    {
        if ($role !== "") {
            $result = $this->execute(
                "SELECT COUNT(*) AS total FROM users WHERE role = ?",
                "s",
                [$role]
            );
        } else {
            $result = $this->execute(
                "SELECT COUNT(*) AS total FROM users"
            );
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }

    public function toggleActive(int $id)
    {
        return $this->execute(
            "UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?",
            "i",
            [$id]
        );
    }

    public function getUsersByRole(string $role)
    {
        $result = $this->execute(
            "SELECT * FROM users WHERE role = ? AND is_active = 1 ORDER BY name ASC",
            "s",
            [$role]
        );

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}