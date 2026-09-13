<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $pdo = Database::getConnection();
    $sql = file_get_contents(__DIR__ . '/doctor_schema.sql');
    
    // Split queries by semicolon if needed or execute directly
    $pdo->exec($sql);
    echo "[SUCCESS] Doctor schema and seed data successfully applied to MedicalRegistrationDB!\n";
    
    // Verify tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "[INFO] Current Tables: " . implode(', ', $tables) . "\n";
    
    // Verify Doctors & Users
    $docCount = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $specCount = $pdo->query("SELECT COUNT(*) FROM specialties")->fetchColumn();
    echo "[INFO] Total Specialties: $specCount | Total Doctors: $docCount | Total Users: $userCount\n";
    
} catch (Exception $e) {
    echo "[ERROR] Migration failed: " . $e->getMessage() . "\n";
}
