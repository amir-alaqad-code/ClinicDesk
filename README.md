# ClinicDesk – Clinic Management Dashboard

ClinicDesk is a secure and private clinic management dashboard built with **PHP**, **MySQL**, and **AdminLTE 3**.

The system is designed for internal clinic operations and supports three main roles:

- **Admin**
- **Doctor**
- **Patient**

Each role has a protected dashboard, specific permissions, and controlled access to the system features.

---

## Developed By

**Dev. Amir Alaqad**

---

## Project Overview

ClinicDesk is a web-based clinic management system that helps clinics manage users, doctors, medical specializations, appointments, prescriptions, reports, and dashboard statistics from one organized interface.

The project follows a simple MVC-like architecture using:

- A front controller through `index.php`
- Controllers for request handling
- Models for database operations
- Views for user interface rendering
- Reusable AdminLTE partials
- Role-based access control
- Secure form processing using CSRF tokens

The system is private and does not include a public registration page.  
All accounts are created and managed by the admin.

---

## Main Roles and Permissions

### Admin

The admin can:

- Login through the shared login page
- Access the admin dashboard
- View dashboard statistics
- Manage users
- Create admin, doctor, and patient accounts
- Search users by name or email
- Filter users by role
- Edit user basic information
- Upload, display, and remove user avatars
- Activate and deactivate users
- Prevent self-deactivation
- Manage doctors
- Edit doctor specialization, bio, consultation fee, and available days
- Manage medical specializations
- Add new specializations
- Delete unused specializations
- Prevent deletion of specializations assigned to doctors
- View all appointments
- Update appointment statuses
- Generate appointment reports
- Export appointment reports as CSV

### Doctor

The doctor can:

- Login through the shared login page
- Access the doctor dashboard
- View only their own appointments
- Update appointment status
- Add doctor notes
- Add prescriptions only after appointment completion
- Upload optional prescription PDF files securely

### Patient

The patient can:

- Login through the shared login page
- Access the patient dashboard
- Book appointments with doctors
- View only their own appointments
- Cancel pending appointments
- View their own prescriptions
- Download prescription PDF files securely

---

## Main Features

### Authentication and Authorization

- Single login page for all roles
- Session-based authentication
- Role-based dashboard redirection
- Role-based access control using `Auth::requireRole()`
- Secure logout using POST request
- CSRF protection for sensitive forms
- Session ID regeneration after successful login
- General login error messages for security
- Inactive users are prevented from logging in

### User Management

The admin can manage all system users.

Supported features:

- List all users
- Search users by name or email
- Filter users by role
- Create users with roles
- Edit user name and phone
- Upload user avatar
- Validate avatar images using `getimagesize()`
- Display uploaded avatars in the users table
- Remove existing avatars
- Activate and deactivate users
- Prevent the current admin from deactivating their own account
- CSRF protection for sensitive actions

### Doctor Management

Doctors are created from the Users panel by selecting the `doctor` role.

Each doctor has:

- A linked user account
- A specialization
- A bio
- A consultation fee
- Available days

The admin can:

- View all doctors
- Edit doctor professional information
- Change doctor specialization
- Update consultation fee
- Update available days
- View doctor status

Doctor photos are handled through the user avatar field for doctor accounts.

### Specialization Management

The admin can manage medical specializations.

Supported features:

- View all specializations
- Add new specializations
- Delete unused specializations
- Prevent deleting specializations that are already assigned to doctors
- CSRF protection for add and delete operations
- DataTables support for listing, search, sorting, and pagination

### Appointment Management

Patients can book appointments with available doctors.

Booking rules:

- Doctor is required
- Date is required
- Time slot is required
- Appointment date cannot be in the past
- Doctor availability is checked before booking
- Double booking is prevented for the same doctor, date, and time

Appointment statuses:

- `pending`
- `confirmed`
- `completed`
- `cancelled`

Role behavior:

- Admin can view all appointments
- Doctor can view only their own appointments
- Patient can view only their own appointments
- Doctor and admin can update appointment status
- Patient can cancel only pending appointments

### Prescription Management

Doctors can add prescriptions after completing appointments.

Prescription rules:

- Appointment must be completed
- Doctor must own the appointment
- Only one prescription is allowed per appointment
- Diagnosis is required
- Medications are required
- Notes are optional
- PDF upload is optional

PDF security:

- PDF file size is validated
- PDF MIME type is validated using `finfo_file()`
- PDF files are stored in a protected upload directory
- Direct access to PDF files is blocked
- PDF download is handled through a secure controller
- Patient can download only their own prescriptions
- Doctor can access only prescriptions related to their own appointments
- Admin access is controlled by role permissions

### Dashboard Statistics

The system includes role-specific dashboards.

#### Admin Dashboard

The admin dashboard shows:

- Number of admins
- Number of doctors
- Number of patients
- Number of appointments today
- Appointment status summary for the current week
- Recent appointments

#### Doctor Dashboard

The doctor dashboard shows:

- This month’s appointments
- Pending appointments
- Completed appointments
- Upcoming appointments

#### Patient Dashboard

The patient dashboard shows:

- Active appointments
- Completed appointments
- Prescription count
- Next upcoming appointment

### Reports and CSV Export

The admin can generate appointment reports using:

- Start date
- End date
- Doctor filter
- Status filter

Report data includes:

- Patient name
- Doctor name
- Specialization
- Appointment date
- Appointment time
- Status
- Reason

Reports can be exported as:

```text
CSV
```

CSV export is generated using PHP and downloaded as a file.

---

## AdminLTE 3 Integration

The project uses **AdminLTE 3** locally from:

```text
public/assets/adminlte/
```

No external CDN is required.

Implemented AdminLTE features:

- Login card
- Dashboard layout
- Sidebar navigation
- Navbar
- Cards
- Small boxes
- Badges
- Alerts
- Buttons
- Breadcrumbs
- DataTables
- Responsive tables

Reusable partials:

```text
views/partials/header.php
views/partials/navbar.php
views/partials/sidebar.php
views/partials/footer.php
views/partials/alerts.php
```

---

## DataTables

DataTables are enabled for listing pages to provide:

- Client-side search
- Sorting
- Pagination
- Responsive table behavior
- Show entries control

Used in pages such as:

- Users
- Doctors
- Specializations
- Appointments
- Prescriptions
- Reports

---

## Breadcrumbs

Breadcrumbs are added inside page headers to improve navigation.

Examples:

```text
Dashboard > Users
Dashboard > Doctors
Dashboard > Appointments
Dashboard > Reports
```

Sub-pages include deeper breadcrumbs such as:

```text
Dashboard > Users > Create User
Dashboard > Users > Edit User
Dashboard > Doctors > Edit Doctor
Dashboard > Appointments > Book Appointment
Dashboard > Appointments > Add Prescription
```

---

## Security Features

The project includes the following security practices:

- Password hashing using `password_hash()`
- Password verification using `password_verify()`
- Prepared statements for SQL queries
- CSRF token generation and validation
- Role-based access control
- Session-based authentication
- Session ID regeneration after login
- Output escaping using `htmlspecialchars()`
- Inactive account login prevention
- Ownership checks for appointments and prescriptions
- Avatar validation using `getimagesize()`
- PDF validation using `finfo_file()`
- Protected upload directories
- Secure prescription download through controller
- Direct prescription file access prevention using `.htaccess`
- Sensitive database configuration excluded from GitHub

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
- DataTables
- XAMPP

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

The database export file is included as:

```text
clinicdesk_db.sql
```

### Database Tables

#### users

Stores all system accounts:

- Admin users
- Doctor users
- Patient users

Important columns:

- `id`
- `name`
- `email`
- `password`
- `role`
- `phone`
- `avatar`
- `is_active`
- `created_at`

#### specializations

Stores medical specializations.

Important columns:

- `id`
- `name`

Specialization names are unique.

#### doctors

Stores doctor profile details.

Important columns:

- `id`
- `user_id`
- `specialization_id`
- `bio`
- `consultation_fee`
- `available_days`

Each doctor is connected to one user account.

#### appointments

Stores appointment bookings.

Important columns:

- `id`
- `patient_id`
- `doctor_id`
- `appt_date`
- `appt_time`
- `status`
- `reason`
- `doctor_notes`
- `created_at`

The database prevents duplicate bookings for the same doctor at the same date and time.

#### prescriptions

Stores prescriptions connected to appointments.

Important columns:

- `id`
- `appointment_id`
- `diagnosis`
- `medications`
- `notes`
- `file_path`
- `created_at`

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

### 5. Import the SQL File

Import:

```text
clinicdesk_db.sql
```

### 6. Configure Database Connection

Copy this file:

```text
config/database.example.php
```

Rename the copied file to:

```text
config/database.php
```

Update the values if needed:

```php
<?php

define("DB_HOST", "localhost");
define("DB_NAME", "clinicdesk_db");
define("DB_USER", "root");
define("DB_PASS", "");
```

Important:

```text
config/database.php is local-only and should not be committed to GitHub.
```

### 7. Run the Project

If Apache uses port `8080`:

```text
http://localhost:8080/clinicdesk/
```

If Apache uses the default port `80`:

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

1. Login as admin.
2. Open the dashboard.
3. Open Users.
4. Search users by name or email.
5. Filter users by role.
6. Create a patient account.
7. Create a doctor account.
8. Edit user name and phone.
9. Upload a user avatar.
10. Remove a user avatar.
11. Toggle user active status.
12. Confirm the admin cannot deactivate their own account.
13. Open Doctors.
14. Edit doctor specialization, bio, fee, and available days.
15. Open Specializations.
16. Add a new specialization.
17. Delete an unused specialization.
18. Try deleting a used specialization and confirm deletion is blocked.
19. Open Appointments.
20. View all appointments.
21. Open Reports.
22. Generate a report.
23. Export CSV.

### Doctor Testing

1. Login as doctor.
2. Open the doctor dashboard.
3. Open Appointments.
4. View own appointments only.
5. Confirm appointment.
6. Complete appointment.
7. Add prescription.
8. Upload optional PDF prescription.

### Patient Testing

1. Login as patient.
2. Open the patient dashboard.
3. Open Appointments.
4. Book an appointment.
5. Try booking the same doctor, date, and time again.
6. Confirm duplicate booking is blocked.
7. Cancel a pending appointment.
8. Open Prescriptions.
9. Download prescription PDF if available.

---

## GitHub Submission Checklist

Before submission, make sure the repository includes:

- Full project source code
- `README.md`
- `clinicdesk_db.sql`
- `.gitignore`
- `config/database.example.php`
- AdminLTE local assets
- Protected upload folders
- Meaningful commits

Make sure the repository does not include:

- `config/database.php`
- Real uploaded prescription PDF files
- Private uploaded user files
- Backup folders
- ZIP archives

---

## Final Project Checklist

- Login works for admin
- Login works for doctor
- Login works for patient
- Logout works without CSRF errors
- Admin dashboard works
- Doctor dashboard works
- Patient dashboard works
- Users listing works
- User search works
- User role filter works
- User creation works
- User edit works
- Avatar upload works
- Avatar validation works
- Avatar removal works
- User active/inactive toggle works
- Admin self-deactivation is blocked
- Doctors listing works
- Doctor edit works
- Specializations listing works
- Specialization add works
- Specialization safe delete works
- Appointment booking works
- Past date booking is blocked
- Doctor availability validation works
- Appointment conflict check works
- Appointment status update works
- Patient appointment cancellation works
- Prescription creation works
- Duplicate prescription creation is blocked
- PDF upload works
- PDF validation works
- Secure PDF download works
- Direct PDF access is blocked
- Dashboard statistics work
- Reports work
- CSV export works
- DataTables work
- Breadcrumbs appear in pages
- SQL export file is included
- README is complete and updated
- GitHub repository is clean

---

## Important Notes

- This project is for internal clinic management.
- There is no public registration page.
- All accounts are created by the admin.
- AdminLTE assets are stored locally.
- Prescription files are protected from direct access.
- Uploaded files should not be committed to GitHub.
- `config/database.php` should remain local.
- Doctor photos are handled through the user avatar field for doctor accounts.

---

## Developer

**Dev. Amir Alaqad**
