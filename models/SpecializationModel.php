<?php

require_once __DIR__ . "/BaseModel.php";

class SpecializationModel extends BaseModel
{
    // Return all specializations ordered by name
    public function getAll(): array
    {
        $sql = "
            SELECT id, name
            FROM specializations
            ORDER BY name ASC
        ";

        $result = $this->execute($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Find one specialization by ID
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM specializations WHERE id = ? LIMIT 1";

        $result = $this->execute($sql, "i", [$id]);

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    // Create a new specialization
    public function create(string $name): int
    {
        $sql = "INSERT INTO specializations (name) VALUES (?)";

        $success = $this->execute($sql, "s", [$name]);

        if (!$success) {
            return 0;
        }

        return $this->db->lastInsertId();
    }

    // Update specialization name
    public function update(int $id, string $name): bool
    {
        $sql = "UPDATE specializations SET name = ? WHERE id = ?";

        return (bool) $this->execute($sql, "si", [$name, $id]);
    }

    // Check if the specialization is not used by any doctor
    public function isSafeToDelete(int $id): bool
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM doctors
            WHERE specialization_id = ?
        ";

        $result = $this->execute($sql, "i", [$id]);

        if (!$result) {
            return false;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"] === 0;
    }

    // Delete specialization only by ID
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM specializations WHERE id = ?";

        return (bool) $this->execute($sql, "i", [$id]);
    }
}