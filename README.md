# ClinicDesk

ClinicDesk is a PHP and MySQL clinic management dashboard for the Web Programming 2 final project.

This repository is being rebuilt step by step to keep a clean Git history with meaningful commits.

## Current step

Step 4 adds the shared security and utility classes: `Auth`, `CSRF`, and `Paginator`. These classes will be used by the authentication flow, protected pages, POST forms, and paginated lists in the next steps.

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

## Step 4 manual testing

1. Open the project in the browser:

```text
http://localhost/clinicdesk/
```

2. Confirm the bootstrap page still loads.
3. Open an unknown route:

```text
http://localhost/clinicdesk/index.php?page=unknown
```

4. Confirm the 404 page appears.
5. Run PHP syntax checks if PHP is available in your terminal:

```bash
php -l core/Auth.php
php -l core/CSRF.php
php -l core/Paginator.php
```

Expected result:

- No syntax errors.
- Existing routing still works.
- The project is ready for the authentication step.
