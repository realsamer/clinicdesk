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


## Step 7: Specialization CRUD

This step adds full admin specialization management:

- List all specializations.
- Add a new specialization.
- Edit an existing specialization.
- Update specialization name.
- Delete specialization only when no doctor is using it.
- Protect all write actions with CSRF validation.
- Restrict access to admin users only.

Route:

```text
index.php?page=specializations
```

Suggested commit:

```bash
git add .
git commit -m "feat: add specialization management crud"
```

## Step 8 - Doctor Management and Doctor Profile

This step adds the doctor management module. Admin users can list and edit doctor records. Doctor users can edit their own professional profile, including specialization, bio, consultation fee, available days, and profile photo. Doctor photo uploads are validated with `getimagesize()` and limited to JPEG/PNG files up to 1 MB.

Test this step with:

- Admin: `admin@clinic.local` / `Admin@1234`
- Doctor: `doctor@clinic.local` / `Admin@1234`

Important pages:

- `index.php?page=doctors`
- `index.php?page=doctors&action=edit&id=1`
- `index.php?page=doctors&action=profile`

Commit message:

```bash
git commit -m "feat: add doctor management and profile editing"
```

## Step 9 - Admin User Management with Avatar Uploads

This step adds admin user management:

- Paginated users list.
- Filter by role.
- Search by name or email.
- Create admin, doctor, and patient accounts.
- Create a doctor record when the admin creates a doctor account.
- Edit user name, phone, avatar, and active status.
- Prevent the logged-in admin from deactivating their own account.
- Change any user's password from the admin panel.
- Validate user avatars with `getimagesize()`.
- Allow only JPEG/PNG avatar images up to 1 MB.

Important pages:

- `index.php?page=users`
- `index.php?page=users&action=create`
- `index.php?page=users&action=edit&id=1`
- `index.php?page=users&action=password&id=1`

Suggested commit:

```bash
git add .
git commit -m "feat: add admin user management with avatar uploads"
```

## Step 10 - Patient Profile Editing

This step adds the patient self-profile page:

- Patient can open `My Profile` from the sidebar.
- Patient can update their own name.
- Patient can update their own phone number.
- Patient can upload/change their avatar.
- Avatar upload accepts only valid JPEG/PNG images.
- Avatar upload is limited to 1 MB.
- Uploaded avatar files are stored in `public/uploads/avatars/`.
- The session name is updated after saving the profile.

Important pages:

- `index.php?page=users&action=profile`
- `index.php?page=users&action=update_profile`

Suggested commit:

```bash
git add .
git commit -m "feat: add patient profile editing"
```

## Step 11 - Patient Appointment Booking and History

This step adds the patient appointment workflow:

- Patient can book an appointment with an active doctor.
- Doctor dropdown shows specialization and available days.
- Appointment date cannot be in the past.
- Appointment time must be one of the fixed 30-minute slots from 09:00 to 16:00.
- Selected date must match the doctor's available days.
- System checks for doctor/date/time conflicts before booking.
- New appointments are created with `pending` status.
- Patient can view their own appointment history only.
- Patient can filter appointments by status and date range.
- Patient can cancel only pending appointments.
- All POST actions are protected with CSRF tokens.

Important pages:

- `index.php?page=appointments&action=book`
- `index.php?page=appointments`

Suggested commit:

```bash
git add .
git commit -m "feat: add patient appointment booking and history"
```

## Step 12 - Doctor appointment schedule and status updates

This step adds the doctor-side appointment workflow:

- Doctors can open **My Schedule** from the sidebar.
- Today's appointments appear at the top of the schedule page.
- Doctors can filter appointments by status and date range.
- Doctors can view appointment details.
- Doctors can confirm pending appointments.
- Doctors can complete confirmed appointments.
- Doctors can cancel pending or confirmed appointments.
- Doctors can add and update notes for their own appointments.
- Server-side ownership checks prevent doctors from accessing appointments that do not belong to them.

Commit message:

```bash
git commit -m "feat: add doctor appointment schedule and status updates"
```

## Step 13 - Admin appointment oversight and filters

This step adds the admin-side appointment workflow:

- Admin can open **All Appointments** from the sidebar.
- Admin can view all appointments in the system.
- Admin can filter appointments by doctor, patient name, status, start date, and end date.
- Admin can open appointment details for any appointment.
- Admin can update any appointment status.
- Filtering is handled with a dynamic WHERE clause using prepared statements.
- Pagination remains active for the admin appointment list.
- Doctor and patient appointment ownership checks still remain enforced for non-admin users.

Important pages:

- `index.php?page=appointments`
- `index.php?page=appointments&action=detail&id=1`

Suggested commit:

```bash
git add .
git commit -m "feat: add admin appointment oversight and filters"
```
