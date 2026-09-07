<?php
/**
 * Tupi Municipal Hospital Master Database Migration Script
 * Runs all schemas in order: schema.sql -> doctor_schema.sql -> tmhis_master_schema.sql
 */

declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';

echo "=== TUPI MUNICIPAL HOSPITAL MASTER DATABASE MIGRATION ===\n";

try {
    $pdo = Database::getConnection();
    echo "Connected to MySQL Server.\n";

    $files = [
        'schema.sql' => __DIR__ . '/schema.sql',
        'seed.sql' => __DIR__ . '/seed.sql',
        'doctor_schema.sql' => __DIR__ . '/doctor_schema.sql',
        'tmhis_master_schema.sql' => __DIR__ . '/tmhis_master_schema.sql',
        'comprehensive_seed.sql' => __DIR__ . '/comprehensive_seed.sql'
    ];

    foreach ($files as $name => $path) {
        if (!file_exists($path)) {
            echo "[WARN] File not found: $name\n";
            continue;
        }

        echo "Applying {$name}... ";
        $sql = file_get_contents($path);
        
        // Execute multi-statement SQL
        $pdo->exec($sql);
        echo "OK\n";
    }

    // Verify tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "\nTotal tables in MedicalRegistrationDB: " . count($tables) . "\n";
    echo "Tables: " . implode(', ', $tables) . "\n";
    echo "\n=== MIGRATION COMPLETED SUCCESSFULLY ===\n";

} catch (PDOException $e) {
    echo "\n[ERROR] Database Migration Failed: " . $e->getMessage() . "\n";
    exit(1);
} catch (Throwable $e) {
    echo "\n[ERROR] Unexpected Exception: " . $e->getMessage() . "\n";
    exit(1);
}
