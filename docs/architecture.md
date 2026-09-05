# CritiCare ICU — Architecture

## Purpose

CritiCare ICU is a local web-based application designed for the intensive care unit.

The application will progressively support:
- Patient administrative registration
- ICU hospitalization management
- Current patient dashboard
- Role-based access control
- Clinical modules
- CLIF-aligned data structure
- Research data extraction

## Initial modules

1. Authentication and user accounts
2. Roles and permissions
3. Patient administrative data
4. ICU hospitalizations
5. Current ICU patient dashboard

## Future clinical modules

- Laboratory results
- Microbiology
- Mechanical ventilation
- Vascular access and PICC lines
- Medications
- Clinical scores
- Research exports

## Data principles

- One central database
- One patient can have multiple hospitalizations
- Clinical modules are linked to a hospitalization
- Patient data remain on the local ICU server
- No real patient data are stored on GitHub
