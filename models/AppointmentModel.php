<?php

require_once __DIR__ . "/BaseModel.php";

class AppointmentModel extends BaseModel
{
    // Book a new appointment
    public function book(array $data): bool
    {
        $sql = "
            INSERT INTO appointments 
            (patient_id, doctor_id, appt_date, appt_time, status, reason)
            VALUES (?, ?, ?, ?, 'pending', ?)
        ";

        return (bool) $this->execute($sql, "iisss", [
            $data["patient_id"],
            $data["doctor_id"],
            $data["appt_date"],
            $data["appt_time"],
            $data["reason"] ?? null
        ]);
    }

    // Check if a doctor already has an appointment at the same date and time
    public function hasConflict(int $doctorId, string $date, string $time): bool
    {
        $sql = "
            SELECT id
            FROM appointments
            WHERE doctor_id = ?
            AND appt_date = ?
            AND appt_time = ?
            AND status != 'cancelled'
            LIMIT 1
        ";

        $result = $this->execute($sql, "iss", [$doctorId, $date, $time]);

        return $result && $result->num_rows > 0;
    }

    // Get appointments for a specific patient
    public function getByPatient(int $patientId, int $page, array $filters = []): array
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit = ITEMS_PER_PAGE;

        $conditions = ["appointments.patient_id = ?"];
        $types = "i";
        $params = [$patientId];

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

        $where = implode(" AND ", $conditions);

        $sql = "
            SELECT 
                appointments.*,
                users.name AS doctor_name,
                specializations.name AS specialization_name
            FROM appointments
            INNER JOIN doctors ON appointments.doctor_id = doctors.id
            INNER JOIN users ON doctors.user_id = users.id
            INNER JOIN specializations ON doctors.specialization_id = specializations.id
            WHERE $where
            ORDER BY appointments.appt_date DESC, appointments.appt_time DESC
            LIMIT ? OFFSET ?
        ";

        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get appointments for a specific doctor
    public function getByDoctor(int $doctorId, int $page, array $filters = []): array
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit = ITEMS_PER_PAGE;

        $conditions = ["appointments.doctor_id = ?"];
        $types = "i";
        $params = [$doctorId];

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

        $where = implode(" AND ", $conditions);

        $sql = "
            SELECT 
                appointments.*,
                users.name AS patient_name,
                users.email AS patient_email
            FROM appointments
            INNER JOIN users ON appointments.patient_id = users.id
            WHERE $where
            ORDER BY appointments.appt_date DESC, appointments.appt_time DESC
            LIMIT ? OFFSET ?
        ";

        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get all appointments for admin
    public function getAll(int $page, array $filters = []): array
    {
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $limit = ITEMS_PER_PAGE;

        $conditions = [];
        $types = "";
        $params = [];

        if (!empty($filters["doctor_id"])) {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = $filters["doctor_id"];
        }

        if (!empty($filters["patient_name"])) {
            $conditions[] = "patients.name LIKE ?";
            $types .= "s";
            $params[] = "%" . $filters["patient_name"] . "%";
        }

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

        $where = "";
        if (!empty($conditions)) {
            $where = "WHERE " . implode(" AND ", $conditions);
        }

        $sql = "
            SELECT 
                appointments.*,
                patients.name AS patient_name,
                doctor_users.name AS doctor_name,
                specializations.name AS specialization_name
            FROM appointments
            INNER JOIN users AS patients ON appointments.patient_id = patients.id
            INNER JOIN doctors ON appointments.doctor_id = doctors.id
            INNER JOIN users AS doctor_users ON doctors.user_id = doctor_users.id
            INNER JOIN specializations ON doctors.specialization_id = specializations.id
            $where
            ORDER BY appointments.created_at DESC
            LIMIT ? OFFSET ?
        ";

        $types .= "ii";
        $params[] = $limit;
        $params[] = $offset;

        $result = $this->execute($sql, $types, $params);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update appointment status and optional doctor notes
    public function updateStatus(int $id, string $status, string $notes = ""): bool
    {
        $sql = "
            UPDATE appointments
            SET status = ?, doctor_notes = ?
            WHERE id = ?
        ";

        return (bool) $this->execute($sql, "ssi", [$status, $notes, $id]);
    }

    // Find appointment by ID with related patient and doctor information
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT 
                appointments.*,
                patients.name AS patient_name,
                patients.email AS patient_email,
                doctor_users.name AS doctor_name,
                specializations.name AS specialization_name
            FROM appointments
            INNER JOIN users AS patients ON appointments.patient_id = patients.id
            INNER JOIN doctors ON appointments.doctor_id = doctors.id
            INNER JOIN users AS doctor_users ON doctors.user_id = doctor_users.id
            INNER JOIN specializations ON doctors.specialization_id = specializations.id
            WHERE appointments.id = ?
            LIMIT 1
        ";

        $result = $this->execute($sql, "i", [$id]);

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    // Count today's appointments for admin dashboard
    public function countToday(): int
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM appointments
        WHERE appt_date = CURDATE()
    ";

        $result = $this->execute($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }

    // Count this week's appointments grouped by status
    public function countThisWeekByStatus(): array
    {
        $sql = "
        SELECT status, COUNT(*) AS total
        FROM appointments
        WHERE YEARWEEK(appt_date, 1) = YEARWEEK(CURDATE(), 1)
        GROUP BY status
    ";

        $result = $this->execute($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Get latest 5 appointments for admin dashboard
    public function getRecentFive(): array
    {
        $sql = "
        SELECT 
            appointments.*,
            patients.name AS patient_name,
            doctor_users.name AS doctor_name
        FROM appointments
        INNER JOIN users AS patients ON appointments.patient_id = patients.id
        INNER JOIN doctors ON appointments.doctor_id = doctors.id
        INNER JOIN users AS doctor_users ON doctors.user_id = doctor_users.id
        ORDER BY appointments.created_at DESC
        LIMIT 5
    ";

        $result = $this->execute($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Count doctor's appointments this month
    public function countDoctorThisMonth(int $doctorId): int
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM appointments
        WHERE doctor_id = ?
        AND MONTH(appt_date) = MONTH(CURDATE())
        AND YEAR(appt_date) = YEAR(CURDATE())
    ";

        $result = $this->execute($sql, "i", [$doctorId]);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }

    // Count doctor's appointments by status
    public function countDoctorByStatus(int $doctorId, string $status): int
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM appointments
        WHERE doctor_id = ?
        AND status = ?
    ";

        $result = $this->execute($sql, "is", [$doctorId, $status]);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }

    // Get next 5 upcoming appointments for a doctor
    public function getDoctorUpcoming(int $doctorId): array
    {
        $sql = "
        SELECT 
            appointments.*,
            users.name AS patient_name
        FROM appointments
        INNER JOIN users ON appointments.patient_id = users.id
        WHERE appointments.doctor_id = ?
        AND appointments.appt_date >= CURDATE()
        AND appointments.status IN ('pending', 'confirmed')
        ORDER BY appointments.appt_date ASC, appointments.appt_time ASC
        LIMIT 5
    ";

        $result = $this->execute($sql, "i", [$doctorId]);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Count patient's active appointments
    public function countPatientActive(int $patientId): int
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM appointments
        WHERE patient_id = ?
        AND status IN ('pending', 'confirmed')
    ";

        $result = $this->execute($sql, "i", [$patientId]);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }

    // Count patient's completed appointments
    public function countPatientCompleted(int $patientId): int
    {
        $sql = "
        SELECT COUNT(*) AS total
        FROM appointments
        WHERE patient_id = ?
        AND status = 'completed'
    ";

        $result = $this->execute($sql, "i", [$patientId]);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int) $row["total"];
    }

    // Get patient's next upcoming appointment
    public function getPatientNextAppointment(int $patientId): ?array
    {
        $sql = "
        SELECT 
            appointments.*,
            doctor_users.name AS doctor_name
        FROM appointments
        INNER JOIN doctors ON appointments.doctor_id = doctors.id
        INNER JOIN users AS doctor_users ON doctors.user_id = doctor_users.id
        WHERE appointments.patient_id = ?
        AND appointments.appt_date >= CURDATE()
        AND appointments.status IN ('pending', 'confirmed')
        ORDER BY appointments.appt_date ASC, appointments.appt_time ASC
        LIMIT 1
    ";

        $result = $this->execute($sql, "i", [$patientId]);

        if (!$result || $result->num_rows === 0) {
            return null;
        }

        return $result->fetch_assoc();
    }

    // Generate admin appointment report
    public function getReport(array $filters): array
    {
        $conditions = [
            "appointments.appt_date BETWEEN ? AND ?"
        ];

        $types = "ss";
        $params = [
            $filters["start_date"],
            $filters["end_date"]
        ];

        if (!empty($filters["doctor_id"])) {
            $conditions[] = "appointments.doctor_id = ?";
            $types .= "i";
            $params[] = (int) $filters["doctor_id"];
        }

        if (!empty($filters["status"])) {
            $conditions[] = "appointments.status = ?";
            $types .= "s";
            $params[] = $filters["status"];
        }

        $where = implode(" AND ", $conditions);

        $sql = "
        SELECT 
            appointments.id,
            patients.name AS patient_name,
            doctor_users.name AS doctor_name,
            specializations.name AS specialization_name,
            appointments.appt_date,
            appointments.appt_time,
            appointments.status,
            appointments.reason
        FROM appointments
        INNER JOIN users AS patients ON appointments.patient_id = patients.id
        INNER JOIN doctors ON appointments.doctor_id = doctors.id
        INNER JOIN users AS doctor_users ON doctors.user_id = doctor_users.id
        INNER JOIN specializations ON doctors.specialization_id = specializations.id
        WHERE $where
        ORDER BY appointments.appt_date ASC, appointments.appt_time ASC
    ";

        $result = $this->execute($sql, $types, $params);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
