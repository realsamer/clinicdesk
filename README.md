# ClinicDesk

ClinicDesk is a PHP and MySQL clinic management dashboard for the Web Programming 2 final project.

This repository is being rebuilt step by step to keep a clean Git history with meaningful commits.

## Current step

Step 3 adds the database schema, local database configuration files, a Singleton `Database` class, and a shared `BaseModel` class.

## Planned stack

- PHP
- MySQL
- XAMPP / WAMP
- AdminLTE 3 local assets
- mysqli prepared statements

## Local development

Copy this folder to your local web server directory:

- XAMPP: `C:\xampp\htdocs\clinicdesk`
- WAMP: `C:\wamp64\www\clinicdesk`

Then open:

```text
http://localhost/clinicdesk/
```

## Database setup

Open phpMyAdmin and import this file:

```text
database/clinicdesk_db.sql
```

The SQL file creates a database named:

```text
clinicdesk_db
```

Default demo login records are seeded for later steps:

```text
Admin:   admin@clinic.local   / Admin@1234
Doctor:  doctor@clinic.local  / Admin@1234
Patient: patient@clinic.local / Admin@1234
```

If your MySQL username or password is different, update:

```text
config/database.php
```

For GitHub, keep real local credentials out of Git. Use `config/database.example.php` as the safe example file.

## Step 3 manual testing

1. Open phpMyAdmin.
2. Import `database/clinicdesk_db.sql`.
3. Confirm the database `clinicdesk_db` exists.
4. Confirm these tables exist:

```text
users
specializations
doctors
appointments
prescriptions
```

5. Confirm the `users` table contains the seeded admin, doctor, and patient records.
6. Open:

```text
http://localhost/clinicdesk/
```

Expected result:

- The bootstrap page still loads.
- No PHP error appears.
- Database files are ready for the model/controller steps.
