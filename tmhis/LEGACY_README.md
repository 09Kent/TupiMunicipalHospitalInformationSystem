# Tupi Municipal Hospital Information Management System (TMHIS)

Official portal and comprehensive Healthcare Information Management System for **Tupi Municipal Hospital**, South Cotabato, Philippines.

---

## 🏥 System Overview

TMHIS is a multi-role hospital management platform designed to streamline outpatient registration, clinical consultations, triage, diagnostics, laboratory operations, pharmacy dispensing, billing, medical records, and executive hospital administration.

### Supported Departmental Roles & Portals:
1. **System Administrator** (`Section/Admin`) — User account management, role assignments, system logs, security audits.
2. **Hospital Chief / Medical Director** (`Section/Chef_Medical_officer`) — Department performance metrics, hospital census, executive reporting.
3. **Medical Records Officer** (`Section/Medical_Officer`) — Master patient index, medical archives, ICD coding, exportable census.
4. **Admitting & Registration Staff** (`Section/Register`) — Patient intake, digital body-map symptom classification, triage priority, queuing.
5. **Attending Physician / Doctor** (`Section/Doctor`) — Clinical consultations, diagnosis, e-prescriptions, laboratory orders, medical certificates.
6. **Nurse on Duty** (`Section/Nurse`) — Vitals monitoring, patient bedside observations, triage queue updates, nursing operational tasks.
7. **Medical Technologist** (`Section/Med_Tech`) — Specimen tracking, laboratory diagnostic workflow, digital result verification.
8. **Pharmacist / Pharmacy Aide** (`Section/Pharmacy`) — Drug inventory, stock tracking, electronic prescription dispensing.
9. **Billing & Cashier Staff** (`Section/Accountant`) — Patient billing invoices, PhilHealth/HMO deductions, official receipts, payment settlement.

---

## 🚀 Running on Localhost

### Prerequisites
- **Web Server:** Apache / Nginx (or [Laragon](https://laragon.org/) / XAMPP)
- **PHP:** 8.1+ (CLI & Web Module)
- **Database:** MySQL 8.0+ / MariaDB

### Quick Start (Laragon)
1. Place this directory inside `C:\laragon\www\main`.
2. Start **All Services** (Apache & MySQL) in Laragon.
3. Open your browser and navigate to:
   ```
   http://localhost/main/
   ```
   *Or with Virtual Hosts enabled:* `http://main.test/`

### Alternative Built-in PHP Server
From the project root:
```bash
php -S localhost:8000
```
Navigate to `http://localhost:8000`.

---

## 🗄️ Database Setup

1. Open MySQL / phpMyAdmin / HeidiSQL at `localhost:3306`.
2. Create database:
   ```sql
   CREATE DATABASE medicalregistrationdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Import database schema and master seed data:
   - `Section/Register/database/tmhis_master_schema.sql`
   - `Section/Register/database/comprehensive_seed.sql`

---

## 🧪 System Diagnostics & Health Checks

Run the automated verification test suites from the command line:
```bash
php Section/run_all_tests.php
php Section/system_health_check.php
```

---

## 📄 License
Internal healthcare information system developed for Tupi Municipal Hospital, South Cotabato.
