# ClinicDesk

ClinicDesk is a PHP and MySQL clinic management dashboard for the Web Programming 2 final project.

The system is a private dashboard with one login page and three roles:

- **Admin**: manages users, doctors, specializations, appointments, and reports.
- **Doctor**: manages own schedule, appointment statuses, notes, profile, and prescriptions.
- **Patient**: books appointments, views appointment history, downloads prescriptions, and edits profile.

## Technologies

- PHP
- MySQL
- mysqli prepared statements
- XAMPP / WAMP
- AdminLTE 3 local assets
- HTML, CSS, Bootstrap/AdminLTE components

## Main Features

- Session-based authentication.
- Role-based access control.
- First-login password change support.
- CSRF protection for POST forms.
- Singleton database connection.
- OOP models extending `BaseModel`.
- Prepared statements for database queries.
- Admin user management with avatar uploads.
- Doctor management and doctor profile photo upload.
- Specialization CRUD.
- Patient appointment booking with conflict checks.
- Doctor appointment schedule and status updates.
- Admin appointment oversight with filters.
- Prescription creation with optional PDF upload.
- Secure prescription downloads through PHP controller.
- Role-based dashboard statistics.
- Admin reports with CSV export.

## Project Structure

```text
clinicdesk/
├── index.php
├── .htaccess
├── README.md
├── config/
├── core/
├── models/
├── controllers/
├── views/
├── public/
│   ├── assets/adminlte/
│   └── uploads/
└── database/
    └── clinicdesk_db.sql
```

## Local Installation

1. Copy the `clinicdesk` folder to your local server directory:

```text
XAMPP: C:\xampp\htdocs\clinicdesk
WAMP:  C:\wamp64\www\clinicdesk
```

2. Start Apache and MySQL.

3. Open phpMyAdmin and import:

```text
database/clinicdesk_db.sql
```

4. Confirm that the database name is:

```text
clinicdesk_db
```

5. Update database credentials if needed:

```text
config/database.php
```

Default XAMPP settings are:

```php
const DB_HOST = 'localhost';
const DB_NAME = 'clinicdesk_db';
const DB_USER = 'root';
const DB_PASS = '';
```

6. Open the project:

```text
http://localhost/clinicdesk/
```

## Demo Accounts

After importing the SQL file, use these accounts:

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@clinic.local | Admin@1234 |
| Doctor | doctor@clinic.local | Admin@1234 |
| Patient | patient@clinic.local | Admin@1234 |

If first-login enforcement is active for an account, the system will ask for a password change after login.

## Important Pages

### Admin

```text
index.php?page=dashboard
index.php?page=users
index.php?page=doctors
index.php?page=specializations
index.php?page=appointments
index.php?page=reports
```

### Doctor

```text
index.php?page=dashboard
index.php?page=appointments
index.php?page=doctors&action=profile
```

### Patient

```text
index.php?page=dashboard
index.php?page=appointments&action=book
index.php?page=appointments
index.php?page=prescriptions
index.php?page=users&action=profile
```

## Security Notes

- Passwords use `password_hash()` and `password_verify()`.
- Login calls `session_regenerate_id(true)`.
- POST forms include CSRF tokens.
- All database access goes through prepared statements.
- User output is escaped with `htmlspecialchars()` through the `e()` helper.
- Prescription PDF files are not linked directly. They are served through a controller after role and ownership checks.
- Upload folders include `index.php` files to prevent browsing.
- `public/uploads/prescriptions/.htaccess` blocks direct access to prescription files.

## GitHub Notes

For GitHub submission, keep local credentials out of Git:

```text
config/database.php
```

Use this file as the safe template:

```text
config/database.example.php
```

## Final Commit for Step 16

```bash
git add .
git commit -m "feat: add reports csv export and final documentation"
```
