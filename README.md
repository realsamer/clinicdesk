# ClinicDesk

ClinicDesk is a PHP and MySQL clinic management dashboard for the Web Programming 2 final project.

This repository is being rebuilt step by step to keep a clean Git history with meaningful commits.

## Current step

Step 5 adds the shared AdminLTE dashboard layout and local frontend assets.

Added in this step:

- AdminLTE 3 local asset files under `public/assets/adminlte/`
- Shared layout partials:
  - `views/partials/header.php`
  - `views/partials/navbar.php`
  - `views/partials/sidebar.php`
  - `views/partials/footer.php`
  - `views/partials/alerts.php`
  - `views/partials/paginator.php`
- A temporary dashboard preview in `index.php`

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

## Step 5 manual testing

1. Open the project in the browser:

```text
http://localhost/clinicdesk/
```

2. Confirm the AdminLTE dashboard preview appears.
3. Confirm the dark sidebar and top navbar are visible.
4. Confirm the small statistic boxes and card styling appear correctly.
5. Open browser DevTools > Network and confirm these files load with status `200`:

```text
public/assets/adminlte/dist/css/adminlte.min.css
public/assets/adminlte/dist/js/adminlte.min.js
public/assets/adminlte/plugins/jquery/jquery.min.js
public/assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js
public/assets/adminlte/plugins/fontawesome-free/css/all.min.css
```

6. Open an unknown route:

```text
http://localhost/clinicdesk/index.php?page=unknown
```

Expected result:

- The AdminLTE preview layout loads correctly.
- No red Console errors for missing CSS or JavaScript files.
- Unknown routes still show the 404 page.

## Step 5 commit

```bash
git add .
git commit -m "feat: add adminlte dashboard layout partials"
```

## Step 6 - Authentication flow

Implemented in this step:

- Login page using AdminLTE login layout.
- Login POST handling with CSRF validation.
- `UserModel::findByEmail()` for authentication lookup.
- `password_verify()` for password validation.
- Active account check before login.
- `session_regenerate_id(true)` through `Auth::login()`.
- Logout as POST only with CSRF token.
- Mandatory first-login password change support.
- Temporary protected dashboard preview after login.

Demo accounts after importing `database/clinicdesk_db.sql`:

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@clinic.local | Admin@1234 |
| Doctor | doctor@clinic.local | Admin@1234 |
| Patient | patient@clinic.local | Admin@1234 |

Suggested commit:

```bash
git add .
git commit -m "feat: implement secure authentication flow"
```

