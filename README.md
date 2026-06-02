# ClinicDesk – Clinic Management Dashboard

ClinicDesk is a private clinic management dashboard built with **PHP**, **MySQL**, and **AdminLTE 3**.  
The system is designed for three user roles: **Admin**, **Doctor**, and **Patient**, each with specific permissions and dashboard access.

---

## Project Overview

ClinicDesk helps a clinic manage its internal operations from one secure dashboard.  
It includes user management, doctor management, appointment booking, prescription handling, secure PDF uploads, dashboards, and appointment reports.

This project was developed as a PHP final project by:

**Dev. Amir Alaqad**

---

## Main Features

### Authentication & Authorization

- Single login page for all users
- Session-based authentication
- Role-based access control
- Protected dashboard pages
- Secure logout using POST request
- CSRF protection for forms

### Admin Features

- Manage system users
- Create admin, doctor, and patient accounts
- Activate and deactivate users
- Prevent admin from deactivating their own account
- Manage doctors and their professional data
- View all appointments
- Generate appointment reports
- Export reports as CSV

### Doctor Features

- View own appointment schedule
- Update appointment status
- Add doctor notes
- Add prescriptions after completed appointments
- Upload prescription PDF files securely

### Patient Features

- Book appointments with doctors
- View own appointments
- Cancel pending appointments
- View own prescriptions
- Download prescription PDF files securely

---

## Technologies Used

- PHP
- MySQL
- MySQLi Prepared Statements
- AdminLTE 3
- Bootstrap
- HTML
- CSS
- JavaScript
- Font Awesome

---

## Security Features

- Password hashing using `password_hash()`
- Password verification using `password_verify()`
- CSRF token validation for POST requests
- Prepared statements for database queries
- Role-based access control using `Auth::requireRole()`
- XSS protection using `htmlspecialchars()`
- Session ID regeneration after login
- Secure PDF file validation using `finfo_file()`
- Protected prescription file download through PHP controller
- Direct access to prescription uploads is blocked by `.htaccess`

---

## Project Structure

```text
clinicdesk/
├── index.php
├── .htaccess
├── README.md
├── clinicdesk_db.sql
├── config/
│   ├── config.php
│   ├── database.php
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
│   ├── AppointmentController.php
│   ├── PrescriptionController.php
│   └── ReportController.php
├── models/
│   ├── BaseModel.php
│   ├── UserModel.php
│   ├── DoctorModel.php
│   ├── AppointmentModel.php
│   ├── PrescriptionModel.php
│   └── SpecializationModel.php
├── views/
│   ├── auth/
│   ├── dashboard/
│   ├── users/
│   ├── doctors/
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

Main tables:

- `users`
- `specializations`
- `doctors`
- `appointments`
- `prescriptions`

---

## Installation Guide

### 1. Clone or Download the Project

Place the project folder inside XAMPP `htdocs`:

```text
C:\xampp\htdocs\clinicdesk
```

### 2. Create the Database

Open phpMyAdmin and create a database named:

```text
clinicdesk_db
```

### 3. Import the Database File

Import the SQL file:

```text
clinicdesk_db.sql
```

### 4. Configure Database Connection

Copy:

```text
config/database.example.php
```

Rename it to:

```text
config/database.php
```

Then update the database credentials if needed:

```php
<?php

define("DB_HOST", "localhost");
define("DB_NAME", "clinicdesk_db");
define("DB_USER", "root");
define("DB_PASS", "");
```

### 5. Run the Project

If Apache is running on port `8080`, open:

```text
http://localhost:8080/clinicdesk/
```

If Apache is running on the default port `80`, open:

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

## Test Accounts

You can create doctor and patient accounts from the Admin panel after logging in.

Example:

```text
Patient Email: patient@test.com
Doctor Email: doctor@test.com
```

Passwords depend on the temporary password entered by the admin during account creation.

---

## Important Notes

- Do not upload `config/database.php` to GitHub.
- Use `config/database.example.php` as a safe sample configuration file.
- Do not upload real prescription PDF files.
- Keep uploaded prescription files protected.
- AdminLTE assets must be stored locally inside `public/assets/adminlte/`.
- The system does not use a public registration page; accounts are created by the admin.

---

## GitHub Submission Checklist

Before submitting the project, make sure the repository includes:

- Full project source code
- `README.md`
- `clinicdesk_db.sql`
- `.gitignore`
- `config/database.example.php`
- AdminLTE local assets
- Clear and meaningful commits

Make sure the repository does **not** include:

- `config/database.php`
- Real uploaded PDF prescriptions
- Sensitive user files
- Local backup folders

---

## Developed By

**Dev. Amir Alaqad**
