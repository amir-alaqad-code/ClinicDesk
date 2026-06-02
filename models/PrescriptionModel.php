<?php

require_once __DIR__ . "/BaseModel.php";

class PrescriptionModel extends BaseModel
{
    // Find prescription by appointment ID
    public function findByAppointmentId(int $apptId): ?array
    {
        $sql = "
            SELECT *
            FROM prescriptions
            WHERE appointment_id = ?
            LIMIT 1
        ";

        $result = $this->execute($sql, "i", [$apptId]);

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    // Create a new prescription
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO prescriptions 
            (appointment_id, diagnosis, medications, notes, file_path)
            VALUES (?, ?, ?, ?, ?)
        ";

        $success = $this->execute($sql, "issss", [
            $data["appointment_id"],
            $data["diagnosis"],
            $data["medications"],
            $data["notes"] ?? null,
            $data["file_path"] ?? null
        ]);

        if (!$success) {
            return 0;
        }

        return $this->db->lastInsertId();
    }

    // Update prescription data
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE prescriptions
            SET diagnosis = ?, medications = ?, notes = ?, file_path = ?
            WHERE id = ?
        ";

        return (bool) $this->execute($sql, "ssssi", [
            $data["diagnosis"],
            $data["medications"],
            $data["notes"] ?? null,
            $data["file_path"] ?? null,
            $id
        ]);
    }

    // Get all prescriptions that belong to a specific patient
    public function getByPatient(int $patientId): array
    {
        $sql = "
            SELECT 
                prescriptions.*,
                appointments.appt_date,
                doctor_users.name AS doctor_name,
                specializations.name AS specialization_name
            FROM prescriptions
            INNER JOIN appointments ON prescriptions.appointment_id = appointments.id
            INNER JOIN doctors ON appointments.doctor_id = doctors.id
            INNER JOIN users AS doctor_users ON doctors.user_id = doctor_users.id
            INNER JOIN specializations ON doctors.specialization_id = specializations.id
            WHERE appointments.patient_id = ?
            ORDER BY prescriptions.created_at DESC
        ";

        $result = $this->execute($sql, "i", [$patientId]);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Count prescriptions available for a patient
    public function countByPatient(int $patientId): int
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM prescriptions
        INNER JOIN appointments ON prescriptions.appointment_id = appointments.id
        WHERE appointments.patient_id = ?
    ";

        $result = $this->execute($sql, "i", [$patientId]);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }
}
