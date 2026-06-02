<?php

require_once __DIR__ . "/BaseModel.php";

class DoctorModel extends BaseModel
{
    // Find doctor data by the related user ID
    public function findByUserId(int $userId): ?array
    {
        $sql = "
            SELECT 
                doctors.*,
                users.name,
                users.email,
                users.phone,
                specializations.name AS specialization_name
            FROM doctors
            INNER JOIN users ON doctors.user_id = users.id
            INNER JOIN specializations ON doctors.specialization_id = specializations.id
            WHERE doctors.user_id = ?
            LIMIT 1
        ";

        $result = $this->execute($sql, "i", [$userId]);

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    // Return all doctors for dropdown lists
    public function getAll(): array
    {
        $sql = "
            SELECT 
                doctors.id,
                users.name,
                specializations.name AS specialization_name,
                doctors.consultation_fee,
                doctors.available_days
            FROM doctors
            INNER JOIN users ON doctors.user_id = users.id
            INNER JOIN specializations ON doctors.specialization_id = specializations.id
            WHERE users.is_active = 1
            ORDER BY users.name ASC
        ";

        $result = $this->execute($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Return paginated doctors for the admin panel
    public function getAllPaginated(int $page): array
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit = ITEMS_PER_PAGE;

        $sql = "
            SELECT 
                doctors.id,
                doctors.user_id,
                users.name,
                users.email,
                users.phone,
                users.is_active,
                specializations.name AS specialization_name,
                doctors.bio,
                doctors.consultation_fee,
                doctors.available_days
            FROM doctors
            INNER JOIN users ON doctors.user_id = users.id
            INNER JOIN specializations ON doctors.specialization_id = specializations.id
            ORDER BY users.name ASC
            LIMIT ? OFFSET ?
        ";

        $result = $this->execute($sql, "ii", [$limit, $offset]);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Create a new doctor record
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO doctors 
            (user_id, specialization_id, bio, consultation_fee, available_days)
            VALUES (?, ?, ?, ?, ?)
        ";

        $success = $this->execute($sql, "iisds", [
            $data["user_id"],
            $data["specialization_id"],
            $data["bio"] ?? null,
            $data["consultation_fee"],
            $data["available_days"]
        ]);

        if (!$success) {
            return 0;
        }

        return $this->db->lastInsertId();
    }

    // Update doctor professional data
    public function update(int $doctorId, array $data): bool
    {
        $sql = "
            UPDATE doctors
            SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ?
            WHERE id = ?
        ";

        return (bool) $this->execute($sql, "isdsi", [
            $data["specialization_id"],
            $data["bio"] ?? null,
            $data["consultation_fee"],
            $data["available_days"],
            $doctorId
        ]);
    }

    // Return available days as an array
    public function getAvailableDays(int $doctorId): array
    {
        $sql = "SELECT available_days FROM doctors WHERE id = ? LIMIT 1";

        $result = $this->execute($sql, "i", [$doctorId]);

        if (!$result || $result->num_rows === 0) {
            return [];
        }

        $row = $result->fetch_assoc();

        return explode(",", $row["available_days"]);
    }

    // Count all doctors
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) AS total FROM doctors";

        $result = $this->execute($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }
}