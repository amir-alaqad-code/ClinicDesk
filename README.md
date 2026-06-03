# ClinicDesk – Clinic Management Dashboard

ClinicDesk is a secure clinic management dashboard built with **PHP**, **MySQL**, and **AdminLTE 3**.  
The system is designed for internal clinic use and supports three main roles:

- **Admin**
- **Doctor**
- **Patient**

Each role has its own dashboard, permissions, and allowed actions.

---

## Developed By

**Dev. Amir Alaqad**

---

## Project Description

ClinicDesk is a private web-based clinic management system that helps manage users, doctors, appointments, prescriptions, reports, and dashboard statistics from one organized interface.

The project follows a simple MVC-like structure using:

- Front Controller routing through `index.php`
- Controllers for request handling
- Models for database operations
- Views for UI rendering
- Reusable AdminLTE partials
- Role-based access control
- Secure form handling with CSRF protection

The system does not include public registration.  
All accounts are created and managed by the admin.

---

## Main Roles

### Admin

The admin can:

- Login from the shared login page
- View the admin dashboard
- Manage users
- Create admin, doctor, and patient accounts
- Edit user basic information
- Search users by name or email
- Filter users by role
- Activate and deactivate users
- Prevent self-deactivation
- Manage doctors
- Edit doctor professional information
- Manage medical specializations
- Add and delete specializations safely
- View all appointments
- Update appointment statuses
- Generate appointment reports
- Export reports as CSV

### Doctor

The doctor can:

- Login from the shared login page
- View the doctor dashboard
- View only their own appointments
- Update appointment status
- Add doctor notes
- Add prescriptions after completed appointments
- Upload prescription PDF files securely

### Patient

The patient can:

- Login from the shared login page
- View the patient dashboard
- Book appointments with doctors
- View their own appointments
- Cancel pending appointments
- View their own prescriptions
- Download prescription PDF files securely

---

## Main Features

### Authentication

- One login page for all roles
- Session-based authentication
- Role-based dashboard redirection
- Secure logout using POST
- Session regeneration after login
- General error messages for invalid login attempts

### User Management

- List all users
- Filter users by role
- Search users by name or email
- Create users with roles
- Edit user name and phone
- Activate or deactivate users
- Prevent admin from deactivating their own account
- CSRF protection for sensitive actions

### Doctor Management

- Doctors are created from the Users panel by selecting role `doctor`
- Each doctor has a linked user account
- Doctor profile includes:
  - Specialization
  - Bio
  - Consultation fee
  - Available days
- Admin can edit doctor professional information

### Specialization Management

- Admin can view all specializations
- Admin can add new specializations
- Admin can delete unused specializations
- Used specializations cannot be deleted if assigned to doctors

### Appointment Management

- Patient can book appointments
- Doctor availability is checked before booking
- Appointment date cannot be in the past
- Double booking is prevented
- Appointments have statuses:
  - `pending`
  - `confirmed`
  - `completed`
  - `cancelled`
- Patient can cancel only pending appointments
- Doctor and admin can update appointment status
- Admin can view all appointments
- Doctor can view only own appointments
- Patient can view only own appointments

### Prescription Management

- Doctor can add prescription only after appointment completion
- One prescription is allowed per appointment
- Diagnosis and medications are required
- Optional PDF upload is supported
- PDF files are validated using MIME type checking
- Patient can view and download only their own prescriptions
- Prescription download is handled through a secure controller
- Direct access to uploaded prescription files is blocked

### Dashboards

The system includes role-specific dashboards:

#### Admin Dashboard

- Number of admins
- Number of doctors
- Number of patients
- Appointments today
- This week appointment status summary
- Recent appointments

#### Doctor Dashboard

- Monthly appointment count
- Pending appointment count
- Completed appointment count
- Upcoming appointments

#### Patient Dashboard

- Active appointments
- Completed appointments
- Prescription count
- Next upcoming appointment

### Reports

Admin can generate appointment reports using:

- Start date
- End date
- Doctor filter
- Status filter

Reports include:

- Patient name
- Doctor name
- Specialization
- Date
- Time
- Status
- Reason

Reports can be exported as:

```text
CSV
```

---

## Technologies Used

- PHP
- MySQL
- MySQLi Prepared Statements
- AdminLTE 3
- Bootstrap
- Font Awesome
- HTML
- CSS
- JavaScript
- XAMPP

---

## Security Features

The project includes several important security practices:

- Password hashing using `password_hash()`
- Password verification using `password_verify()`
- CSRF protection for POST forms
- Prepared statements for SQL queries
- Role-based access control using `Auth::requireRole()`
- Session ID regeneration after login
- Output escaping using `htmlspecialchars()`
- PDF MIME type validation using `finfo_file()`
- Secure prescription PDF download through controller
- Direct file access prevention using `.htaccess`
- Sensitive database configuration excluded from GitHub

---

## Project Structure

```text
clinicdesk/
├── index.php
├── .htaccess
├── .gitignore
├── README.md
├── clinicdesk_db.sql
├── config/
│   ├── config.php
│   └── database.example.php
├── core/
│   ├── Auth.php
│   ├── CSRF.php
│   ├── Database.php
│   ├── Paginator.php
│   └── helpers.php
├── controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── UserController.php
│   ├── DoctorController.php
│   ├── SpecializationController.php
│   ├── AppointmentController.php
│   ├── PrescriptionController.php
│   └── ReportController.php
├── models/
│   ├── BaseModel.php
│   ├── UserModel.php
│   ├── DoctorModel.php
│   ├── SpecializationModel.php
│   ├── AppointmentModel.php
│   └── PrescriptionModel.php
├── views/
│   ├── auth/
│   ├── dashboard/
│   ├── users/
│   ├── doctors/
│   ├── specializations/
│   ├── appointments/
│   ├── prescriptions/
│   ├── reports/
│   ├── errors/
│   └── partials/
└── public/
    ├── assets/
    │   └── adminlte/
    └── uploads/
        ├── avatars/
        ├── doctor_photos/
        └── prescriptions/
```

---

## Database

The project uses a MySQL database named:

```text
clinicdesk_db
```

The database schema is included in:

```text
clinicdesk_db.sql
```

Main database tables:

- `users`
- `specializations`
- `doctors`
- `appointments`
- `prescriptions`

---

## Database Tables Overview

### users

Stores all system accounts:

- Admins
- Doctors
- Patients

Important fields:

- `name`
- `email`
- `password`
- `role`
- `phone`
- `avatar`
- `is_active`
- `created_at`

### specializations

Stores medical specializations.

Examples:

- Cardiology
- Dermatology
- Pediatrics
- Neurology

### doctors

Stores doctor-specific professional data and links each doctor to a user account.

Important fields:

- `user_id`
- `specialization_id`
- `bio`
- `consultation_fee`
- `available_days`

### appointments

Stores appointment bookings between patients and doctors.

Important fields:

- `patient_id`
- `doctor_id`
- `appt_date`
- `appt_time`
- `status`
- `reason`
- `doctor_notes`

The database prevents duplicate bookings for the same doctor at the same date and time.

### prescriptions

Stores prescriptions connected to completed appointments.

Important fields:

- `appointment_id`
- `diagnosis`
- `medications`
- `notes`
- `file_path`

Only one prescription is allowed per appointment.

---

## Installation Guide

### 1. Clone the Repository

```bash
git clone https://github.com/amir-alaqad-code/ClinicDesk.git
```

### 2. Move the Project to XAMPP

Place the project folder inside:

```text
C:\xampp\htdocs\clinicdesk
```

### 3. Start XAMPP Services

Start:

- Apache
- MySQL

### 4. Create the Database

Open phpMyAdmin and create a database named:

```text
clinicdesk_db
```

### 5. Import SQL File

Import the file:

```text
clinicdesk_db.sql
```

### 6. Configure Database Connection

Copy:

```text
config/database.example.php
```

Rename the copy to:

```text
config/database.php
```

Then update the database values if needed:

```php
<?php

define("DB_HOST", "localhost");
define("DB_NAME", "clinicdesk_db");
define("DB_USER", "root");
define("DB_PASS", "");
```

> `config/database.php` is local-only and should not be committed to GitHub.

### 7. Run the Project

If Apache runs on port `8080`:

```text
http://localhost:8080/clinicdesk/
```

If Apache runs on the default port `80`:

```text
http://localhost/clinicdesk/
```

---

## Default Admin Account

```text
Email: admin@clinic.local
Password: Admin@1234
```

---

## Suggested Testing Flow

### Admin Testing

1. Login as admin
2. Open Users page
3. Search by name or email
4. Filter users by role
5. Create a patient account
6. Create a doctor account
7. Edit user name and phone
8. Toggle user active status
9. Open Doctors page
10. Edit doctor specialization, fee, bio, and available days
11. Open Specializations page
12. Add a new specialization
13. Delete an unused specialization
14. Try deleting a used specialization and confirm it is blocked
15. Open Reports page
16. Generate a report
17. Export CSV

### Patient Testing

1. Login as patient
2. Open Appointments
3. Book an appointment with an available doctor
4. Try booking the same slot again and confirm it is blocked
5. Cancel a pending appointment
6. View prescriptions
7. Download prescription PDF if available

### Doctor Testing

1. Login as doctor
2. Open Appointments
3. View own appointments only
4. Confirm appointment
5. Complete appointment
6. Add prescription
7. Upload optional PDF prescription

---

## Important Notes

- This system is for internal clinic use only.
- There is no public registration page.
- Accounts are created by the admin.
- AdminLTE files are stored locally inside the project.
- PDF prescription files should not be accessed directly.
- Uploaded prescription files should not be committed to GitHub.
- `config/database.php` should stay local and private.

---

## GitHub Submission Checklist

Before submitting, make sure the repository includes:

- Project source code
- `README.md`
- `clinicdesk_db.sql`
- `.gitignore`
- `config/database.example.php`
- AdminLTE local assets
- Protected upload folders
- Meaningful commits

Make sure the repository does not include:

- `config/database.php`
- Real prescription PDF files
- Private uploaded files
- Backup folders
- ZIP archives

---

## Final Project Checklist

- Login works for all roles
- Logout works without CSRF errors
- Admin dashboard works
- Doctor dashboard works
- Patient dashboard works
- Users management works
- User search works
- User role filter works
- User edit works
- User toggle active/inactive works
- Doctors management works
- Specializations management works
- Appointment booking works
- Appointment conflict check works
- Appointment status update works
- Prescription creation works
- PDF upload works
- Secure PDF download works
- Reports work
- CSV export works
- SQL file is included
- README is updated
- GitHub repository is clean

---

## Developer

**Dev. Amir Alaqad**
