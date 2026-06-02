-- Create the main database for the ClinicDesk project
CREATE DATABASE IF NOT EXISTS clinicdesk_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

-- Select the project database
USE clinicdesk_db;

-- Drop old tables if they exist, in the correct order because of foreign keys
DROP TABLE IF EXISTS prescriptions;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS specializations;
DROP TABLE IF EXISTS users;

-- Table 1: stores all system users: admin, doctors, and patients
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Full name of the user
    name VARCHAR(120) NOT NULL,

    -- User email, used for login and must be unique
    email VARCHAR(180) NOT NULL UNIQUE,

    -- Hashed password, never store plain text passwords
    password VARCHAR(255) NOT NULL,

    -- User role controls what the user can access
    role ENUM('admin', 'doctor', 'patient') NOT NULL DEFAULT 'patient',

    -- Optional phone number
    phone VARCHAR(20) DEFAULT NULL,

    -- Optional avatar file path
    avatar VARCHAR(255) DEFAULT NULL,

    -- Used to activate or suspend accounts
    is_active TINYINT(1) NOT NULL DEFAULT 1,

    -- Account creation date
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert the first admin account
-- Email: admin@clinic.local
-- Password: Admin@1234
INSERT INTO users (name, email, password, role)
VALUES (
    'Admin',
    'admin@clinic.local',
    '$2y$12$kn6z9aBniyO2.MR6uksnSeT2OiR.ytxsGxo3UhH9.sPpQdKXFhtNy',
    'admin'
);

-- Table 2: stores medical specializations
CREATE TABLE specializations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Specialization name must be unique
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default clinic specializations
INSERT INTO specializations (name) VALUES
('General Practice'),
('Cardiology'),
('Dermatology'),
('Pediatrics'),
('Orthopedics'),
('Neurology'),
('Ophthalmology'),
('ENT'),
('Psychiatry');

-- Table 3: stores doctor-specific data
CREATE TABLE doctors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Each doctor record belongs to one user account
    user_id INT UNSIGNED NOT NULL UNIQUE,

    -- Doctor specialization
    specialization_id INT UNSIGNED NOT NULL,

    -- Optional doctor bio
    bio TEXT DEFAULT NULL,

    -- Doctor consultation fee
    consultation_fee DECIMAL(8,2) NOT NULL DEFAULT 0.00,

    -- Available days stored as comma-separated values, example: Sun,Mon,Wed
    available_days VARCHAR(50) NOT NULL DEFAULT 'Sun,Mon,Tue,Wed,Thu',

    -- If the user is deleted, delete the related doctor record
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    -- Prevent deleting a specialization if doctors still use it
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 4: stores appointment bookings
CREATE TABLE appointments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- The patient is stored as a user id
    patient_id INT UNSIGNED NOT NULL,

    -- The doctor is stored as a doctor id
    doctor_id INT UNSIGNED NOT NULL,

    -- Appointment date
    appt_date DATE NOT NULL,

    -- Appointment time
    appt_time TIME NOT NULL,

    -- Current appointment status
    status ENUM('pending', 'confirmed', 'completed', 'cancelled')
        NOT NULL DEFAULT 'pending',

    -- Short reason for the appointment
    reason VARCHAR(255) DEFAULT NULL,

    -- Notes written by the doctor
    doctor_notes TEXT DEFAULT NULL,

    -- Booking creation date
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Prevent booking the same doctor at the same date and time
    UNIQUE KEY no_double_booking (doctor_id, appt_date, appt_time),

    -- If the patient account is deleted, delete related appointments
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,

    -- If the doctor record is deleted, delete related appointments
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table 5: stores prescriptions for completed appointments
CREATE TABLE prescriptions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- One prescription is allowed for each appointment
    appointment_id INT UNSIGNED NOT NULL UNIQUE,

    -- Diagnosis written by the doctor
    diagnosis TEXT NOT NULL,

    -- Medications written by the doctor
    medications TEXT NOT NULL,

    -- Optional extra notes
    notes TEXT DEFAULT NULL,

    -- Optional uploaded PDF file path
    file_path VARCHAR(255) DEFAULT NULL,

    -- Prescription creation date
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- If the appointment is deleted, delete its prescription
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;