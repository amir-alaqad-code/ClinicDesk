<?php

require_once __DIR__ . "/BaseModel.php";

class UserModel extends BaseModel
{
    // Find one user by ID
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";

        $result = $this->execute($sql, "i", [$id]);

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    // Find one user by email, mainly used during login
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";

        $result = $this->execute($sql, "s", [$email]);

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    // Create a new user and return the new user ID
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO users (name, email, password, role, phone)
            VALUES (?, ?, ?, ?, ?)
        ";

        $success = $this->execute($sql, "sssss", [
            $data["name"],
            $data["email"],
            $data["password"],
            $data["role"],
            $data["phone"] ?? null
        ]);

        if (!$success) {
            return 0;
        }

        return $this->db->lastInsertId();
    }

    // Update basic user profile data
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE users
            SET name = ?, phone = ?, avatar = ?
            WHERE id = ?
        ";

        return (bool) $this->execute($sql, "sssi", [
            $data["name"],
            $data["phone"] ?? null,
            $data["avatar"] ?? null,
            $id
        ]);
    }

    // Update user password
    public function updatePassword(int $id, string $newHash): bool
    {
        $sql = "UPDATE users SET password = ? WHERE id = ?";

        return (bool) $this->execute($sql, "si", [$newHash, $id]);
    }

    // Count users, optionally filtered by role
    public function countAll(string $role = ""): int
    {
        if ($role !== "") {
            $sql = "SELECT COUNT(*) AS total FROM users WHERE role = ?";
            $result = $this->execute($sql, "s", [$role]);
        } else {
            $sql = "SELECT COUNT(*) AS total FROM users";
            $result = $this->execute($sql);
        }

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }

    // Get paginated users, optionally filtered by role
    public function getAllPaginated(int $page, string $role = ""): array
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit = ITEMS_PER_PAGE;

        if ($role !== "") {
            $sql = "
                SELECT id, name, email, role, phone, avatar, is_active, created_at
                FROM users
                WHERE role = ?
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ";

            $result = $this->execute($sql, "sii", [$role, $limit, $offset]);
        } else {
            $sql = "
                SELECT id, name, email, role, phone, avatar, is_active, created_at
                FROM users
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ";

            $result = $this->execute($sql, "ii", [$limit, $offset]);
        }

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Switch user account between active and inactive
    public function toggleActive(int $id): bool
    {
        $sql = "
            UPDATE users
            SET is_active = CASE 
                WHEN is_active = 1 THEN 0 
                ELSE 1 
            END
            WHERE id = ?
        ";

        return (bool) $this->execute($sql, "i", [$id]);
    }

    // Count users grouped by role for admin dashboard
    public function countByRole(): array
    {
        $sql = "
        SELECT role, COUNT(*) AS total
        FROM users
        GROUP BY role
    ";

        $result = $this->execute($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
