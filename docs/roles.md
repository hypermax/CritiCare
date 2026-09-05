# CritiCare ICU — User Roles and Permissions

## Principle

CritiCare uses role-based access control (RBAC).

Each user has one professional role. Access is checked on every request and never relies only on hiding buttons in the interface.

## Roles

| Role | Description |
|---|---|
| ADMIN | Application and user account management |
| SENIOR | Senior intensivist or responsible physician |
| JUNIOR | Junior physician or resident working under supervision |
| INTERN | Medical intern |
| NURSE | ICU nursing staff |

## Initial permissions

| Permission | ADMIN | SENIOR | JUNIOR | INTERN | NURSE |
|---|---:|---:|---:|---:|---:|
| Login | Yes | Yes | Yes | Yes | Yes |
| View current ICU patients | No | Yes | Yes | Yes | Yes |
| Create patient | Yes | Yes | Yes | Limited | No |
| Edit administrative data | Yes | Yes | Limited | Limited | No |
| Create hospitalization | Yes | Yes | Yes | Limited | No |
| Close hospitalization | No | Yes | No | No | No |
| Record death | No | Yes | No | No | No |
| Record transfer | No | Yes | Limited | No | No |
| View clinical data | No | Yes | Yes | Yes | Yes |
| Enter nursing data | No | Yes | Limited | Limited | Yes |
| Manage users | Yes | No | No | No | No |
| Manage roles | Yes | No | No | No | No |
| Export research data | Limited | Yes | No | No | No |
| View audit logs | Yes | Yes | No | No | No |

## Security rules

- Every account belongs to one identified professional.
- Shared accounts are not allowed.
- Passwords are never stored in plain text.
- The least-privilege principle is used.
- All sensitive actions are logged in `audit_logs`.
- A deceased or transferred patient record is not deleted.
