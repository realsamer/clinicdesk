# ClinicDesk

ClinicDesk is a PHP and MySQL clinic management dashboard for the Web Programming 2 final project.

This repository is being rebuilt step by step to keep a clean Git history with meaningful commits.

## Current step

Step 2 adds the application bootstrap, shared configuration, helper functions, a simple front-controller routing shell, and basic 403/404 error pages.

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

At this step, the project only confirms that the bootstrap and routing shell are working. Database, authentication, models, controllers, and dashboard pages will be added in later steps.

## Step 2 manual testing

Open these URLs:

```text
http://localhost/clinicdesk/
http://localhost/clinicdesk/index.php?page=unknown
http://localhost/clinicdesk/index.php?page=error&action=403
```

Expected result:

- The home URL shows the bootstrap success message.
- Unknown pages show the 404 page.
- The 403 test URL shows the 403 page.
