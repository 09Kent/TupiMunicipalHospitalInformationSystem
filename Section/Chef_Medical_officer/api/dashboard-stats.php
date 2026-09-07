<?php
/**
 * Tupi Municipal Hospital Information Management System
 * API: Executive Dashboard Stats (Role 2)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

use TMHIS\Database;

try {
    $db = Database::getConnection();

    // 1. Summary Cards Data
    // Card 1: Today's Patient Census
    $censusStmt = $db->query("SELECT * FROM patient_census ORDER BY census_date DESC LIMIT 1");
    $latestCensus = $censusStmt->fetch() ?: [
        'total_patients' => 126,
        'inpatients' => 48,
        'outpatients' => 62,
        'emergency' => 16,
        'discharged' => 34
    ];

    // Card 2: Monthly Operational Performance
    $perfScore = 92.0;

    // Card 3: Monthly Revenue
    $revStmt = $db->query("SELECT * FROM billing_revenue_summary ORDER BY id DESC LIMIT 1");
    $latestRev = $revStmt->fetch() ?: [
        'total_revenue' => 1284500.00,
        'paid_amount' => 1092000.00,
        'outstanding_balance' => 192500.00,
        'growth_rate' => 8.4
    ];

    // Card 4: DOH Compliance
    $dohStmt = $db->query("SELECT AVG(compliance_score) as avg_score FROM doh_compliance_items");
    $dohScore = round((float)($dohStmt->fetchColumn() ?: 96.0), 1);

    // 2. Hospital Performance Indicators (KPIs)
    $kpiIndicators = [
        ['name' => 'Patient Volume', 'value' => 92, 'color' => 'primary', 'target' => '90%'],
        ['name' => 'Appointment Completion', 'value' => 89, 'color' => 'emerald', 'target' => '85%'],
        ['name' => 'Department Efficiency', 'value' => 94, 'color' => 'indigo', 'target' => '90%'],
        ['name' => 'Staff Activity', 'value' => 96, 'color' => 'amber', 'target' => '95%'],
        ['name' => 'DOH Compliance', 'value' => (int)$dohScore, 'color' => 'violet', 'target' => '95%'],
    ];

    // 3. Recent Hospital Activity (with relative time labels)
    $activities = [
        [
            'title' => 'Patient census updated',
            'desc' => 'Daily patient census recorded 126 active admissions and visits.',
            'time' => '2 minutes ago',
            'type' => 'primary',
            'icon' => 'Users'
        ],
        [
            'title' => 'Department performance updated',
            'desc' => 'Nursing department performance reviewed at 95.5% efficiency.',
            'time' => '15 minutes ago',
            'type' => 'emerald',
            'icon' => 'Building2'
        ],
        [
            'title' => 'Staff activity recorded',
            'desc' => 'Medical Records Officer updated electronic health record.',
            'time' => '32 minutes ago',
            'type' => 'indigo',
            'icon' => 'Activity'
        ],
        [
            'title' => 'Operational report generated',
            'desc' => 'Monthly operational performance report compiled for August 2026.',
            'time' => '1 hour ago',
            'type' => 'amber',
            'icon' => 'BarChart3'
        ],
        [
            'title' => 'Compliance report updated',
            'desc' => 'DOH compliance status reviewed and certified at 96% overall rating.',
            'time' => '2 hours ago',
            'type' => 'violet',
            'icon' => 'ShieldCheck'
        ]
    ];

    // 4. Quick Highlights for Charting
    $censusTrendStmt = $db->query("SELECT census_date, total_patients, inpatients, outpatients, emergency FROM patient_census ORDER BY census_date DESC LIMIT 7");
    $censusTrend = array_reverse($censusTrendStmt->fetchAll());

    $deptPerfStmt = $db->query("SELECT name, performance_score, active_rate, completed_tasks, pending_tasks FROM departments ORDER BY performance_score DESC LIMIT 5");
    $deptHighlights = $deptPerfStmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'summary_cards' => [
                'today_patient_census' => [
                    'value' => (int)$latestCensus['total_patients'],
                    'label' => "TODAY'S PATIENT CENSUS",
                    'description' => "Current patients recorded today",
                    'trend' => "+4.2% vs yesterday",
                    'trend_direction' => 'up',
                    'breakdown' => [
                        'inpatients' => (int)$latestCensus['inpatients'],
                        'outpatients' => (int)$latestCensus['outpatients'],
                        'emergency' => (int)$latestCensus['emergency'],
                        'discharged' => (int)$latestCensus['discharged']
                    ]
                ],
                'monthly_performance' => [
                    'value' => "92%",
                    'label' => "MONTHLY OPERATIONAL PERFORMANCE",
                    'description' => "Overall operational performance",
                    'trend' => "+3.5% vs target",
                    'trend_direction' => 'up',
                    'score_raw' => $perfScore
                ],
                'monthly_revenue' => [
                    'value' => '₱' . number_format((float)$latestRev['total_revenue'], 0),
                    'label' => "MONTHLY REVENUE",
                    'description' => "Current monthly billing and revenue summary",
                    'trend' => "+8.4% growth",
                    'trend_direction' => 'up',
                    'paid' => '₱' . number_format((float)$latestRev['paid_amount'], 0),
                    'outstanding' => '₱' . number_format((float)$latestRev['outstanding_balance'], 0)
                ],
                'doh_compliance' => [
                    'value' => "96%",
                    'label' => "DOH COMPLIANCE",
                    'description' => "Current compliance status",
                    'trend' => "Level 2 Certified",
                    'trend_direction' => 'up',
                    'score_raw' => $dohScore
                ]
            ],
            'kpis' => $kpiIndicators,
            'recent_activity' => $activities,
            'charts' => [
                'census_trend' => $censusTrend,
                'department_highlights' => $deptHighlights,
                'revenue_breakdown' => [
                    'Consultation' => 320000,
                    'Laboratory' => 285000,
                    'Pharmacy' => 410000,
                    'Other Services' => 269500
                ]
            ]
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
