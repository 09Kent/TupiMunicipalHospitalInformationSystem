<?php
// Fix: Update all user password hashes to match 'password123'
require_once __DIR__ . '/../../Doctor/config/Database.php';

$pdo = Database::getConnection();
$hash = password_hash('password123', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET PasswordHash = :hash");
$stmt->execute([':hash' => $hash]);

echo "Updated " . $stmt->rowCount() . " user passwords to valid bcrypt hash for 'password123'.\n";
echo "Hash: $hash\n";

// Verify
$verify = $pdo->query("SELECT Email, Role FROM users ORDER BY UserID")->fetchAll();
foreach ($verify as $u) {
    echo "  ✓ {$u['Email']} ({$u['Role']})\n";
}
