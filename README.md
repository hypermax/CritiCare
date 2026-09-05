# CritiCare ICU

Local web-based Critical Care Information System designed for ICU workflow.

## Main objectives

- Register and manage ICU patients and hospitalizations.
- Display current ICU patients on a clinical dashboard.
- Manage role-based access for senior physicians, junior physicians, interns, and nurses.
- Build progressive clinical modules: laboratory data, microbiology, vascular access, mechanical ventilation, and research exports.
- Align the internal data model with the CLIF data format where applicable.

## Technology stack

- PHP / Laravel
- MySQL or MariaDB
- Bootstrap 5
- Local Debian server
- GitHub private repository

## Security principles

- No real patient data, database exports, passwords, or `.env` files in GitHub.
- Patient data remain only on the local ICU server.
- Every sensitive action must be logged and controlled by user permissions.

## Status

Early development: project foundation and access-control design.
