-- TMHIS Healthcare Management System
-- Schema for Role 2: Hospital Chief / Medical Director

CREATE TABLE IF NOT EXISTS departments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    code TEXT NOT NULL UNIQUE,
    head TEXT NOT NULL,
    total_staff INTEGER DEFAULT 0,
    active_rate REAL DEFAULT 0.0,
    completed_tasks INTEGER DEFAULT 0,
    pending_tasks INTEGER DEFAULT 0,
    performance_score REAL DEFAULT 0.0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS staff_members (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    staff_code TEXT NOT NULL UNIQUE,
    name TEXT NOT NULL,
    role TEXT NOT NULL,
    department_id INTEGER,
    department_name TEXT NOT NULL,
    shift TEXT DEFAULT 'Day Shift (7 AM - 3 PM)',
    email TEXT,
    contact TEXT,
    status TEXT DEFAULT 'Active',
    FOREIGN KEY(department_id) REFERENCES departments(id)
);

CREATE TABLE IF NOT EXISTS staff_activity_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    staff_id INTEGER,
    staff_name TEXT NOT NULL,
    role_title TEXT NOT NULL,
    department_name TEXT NOT NULL,
    activity_type TEXT NOT NULL,
    activity_description TEXT NOT NULL,
    status TEXT DEFAULT 'Completed',
    ip_address TEXT DEFAULT '192.168.10.45',
    logged_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(staff_id) REFERENCES staff_members(id)
);

CREATE TABLE IF NOT EXISTS doctor_consultation_stats (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    doctor_name TEXT NOT NULL,
    department_name TEXT NOT NULL,
    specialty TEXT NOT NULL,
    total_consultations INTEGER DEFAULT 0,
    completed_consultations INTEGER DEFAULT 0,
    cancelled_consultations INTEGER DEFAULT 0,
    avg_daily_consultations REAL DEFAULT 0.0,
    satisfaction_score REAL DEFAULT 95.0,
    recorded_month TEXT DEFAULT 'August 2026'
);

CREATE TABLE IF NOT EXISTS patient_census (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    census_date DATE NOT NULL,
    total_patients INTEGER DEFAULT 0,
    inpatients INTEGER DEFAULT 0,
    outpatients INTEGER DEFAULT 0,
    emergency INTEGER DEFAULT 0,
    discharged INTEGER DEFAULT 0,
    new_admissions INTEGER DEFAULT 0,
    occupancy_rate REAL DEFAULT 0.0
);

CREATE TABLE IF NOT EXISTS appointment_summaries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    summary_date DATE NOT NULL,
    total_appointments INTEGER DEFAULT 0,
    completed INTEGER DEFAULT 0,
    pending INTEGER DEFAULT 0,
    cancelled INTEGER DEFAULT 0,
    no_show INTEGER DEFAULT 0,
    avg_wait_time_minutes INTEGER DEFAULT 18
);

CREATE TABLE IF NOT EXISTS operational_reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    report_code TEXT NOT NULL UNIQUE,
    report_name TEXT NOT NULL,
    category TEXT NOT NULL,
    period_type TEXT NOT NULL,
    period_label TEXT NOT NULL,
    status TEXT DEFAULT 'Finalized',
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP,
    summary_metrics_json TEXT,
    detailed_payload_json TEXT
);

CREATE TABLE IF NOT EXISTS billing_revenue_summary (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    report_period TEXT NOT NULL,
    total_revenue REAL DEFAULT 0.0,
    paid_amount REAL DEFAULT 0.0,
    outstanding_balance REAL DEFAULT 0.0,
    total_services_count INTEGER DEFAULT 0,
    consultation_revenue REAL DEFAULT 0.0,
    lab_revenue REAL DEFAULT 0.0,
    pharmacy_revenue REAL DEFAULT 0.0,
    other_revenue REAL DEFAULT 0.0,
    growth_rate REAL DEFAULT 0.0
);

CREATE TABLE IF NOT EXISTS laboratory_utilization (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    test_name TEXT NOT NULL,
    category TEXT NOT NULL,
    requests_count INTEGER DEFAULT 0,
    completed_count INTEGER DEFAULT 0,
    pending_count INTEGER DEFAULT 0,
    cancelled_count INTEGER DEFAULT 0,
    utilization_rate REAL DEFAULT 0.0,
    avg_turnaround_hours REAL DEFAULT 1.5
);

CREATE TABLE IF NOT EXISTS pharmacy_stocks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    medicine_name TEXT NOT NULL,
    generic_name TEXT NOT NULL,
    dosage TEXT NOT NULL,
    current_stock INTEGER DEFAULT 0,
    min_level INTEGER DEFAULT 50,
    unit TEXT DEFAULT 'Box',
    status TEXT DEFAULT 'Normal',
    expiry_date DATE NOT NULL,
    lot_number TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS doh_compliance_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    domain_name TEXT NOT NULL,
    compliance_score REAL DEFAULT 95.0,
    status TEXT DEFAULT 'Compliant',
    total_indicators INTEGER DEFAULT 25,
    compliant_indicators INTEGER DEFAULT 24,
    last_inspection_date DATE DEFAULT '2026-08-15',
    next_review_date DATE DEFAULT '2026-11-15',
    notes TEXT
);

CREATE TABLE IF NOT EXISTS administrative_notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    category TEXT NOT NULL,
    description TEXT NOT NULL,
    is_read INTEGER DEFAULT 0,
    urgency TEXT DEFAULT 'normal',
    target_section TEXT DEFAULT 'reports',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_trail (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_name TEXT NOT NULL DEFAULT 'Dr. Maria Santos',
    role_title TEXT NOT NULL DEFAULT 'Hospital Chief / Medical Director',
    action_performed TEXT NOT NULL,
    report_affected TEXT DEFAULT '-',
    details TEXT,
    logged_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
