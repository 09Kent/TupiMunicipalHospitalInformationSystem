<?php
// Section/Admin/models/UserManager.php

require_once __DIR__ . '/../config/Database.php';

class UserManager
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT UserID, FirstName, LastName, Username, Role, Email, Status, CreatedAt, UpdatedAt FROM users ORDER BY UserID ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT UserID, FirstName, LastName, Username, Role, Email, Status, CreatedAt, UpdatedAt FROM users WHERE UserID = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $password = $data['Password'] ?? 'password123';
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (FirstName, LastName, Username, PasswordHash, Role, Email, Status)
                VALUES (:FirstName, :LastName, :Username, :PasswordHash, :Role, :Email, :Status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':FirstName' => $data['FirstName'],
            ':LastName' => $data['LastName'],
            ':Username' => strtolower(trim($data['Username'])),
            ':PasswordHash' => $hash,
            ':Role' => $data['Role'] ?? 'Staff',
            ':Email' => $data['Email'] ?? null,
            ':Status' => $data['Status'] ?? 'Active'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE users SET
                FirstName = :FirstName,
                LastName = :LastName,
                Role = :Role,
                Email = :Email,
                Status = :Status
                WHERE UserID = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':FirstName' => $data['FirstName'],
            ':LastName' => $data['LastName'],
            ':Role' => $data['Role'],
            ':Email' => $data['Email'] ?? null,
            ':Status' => $data['Status'] ?? 'Active'
        ]);
    }

    public function toggleStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET Status = :status WHERE UserID = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function resetPassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET PasswordHash = :hash WHERE UserID = :id");
        return $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        // Don't delete system administrator (UserID 1)
        if ($id === 1) return false;
        $stmt = $this->db->prepare("DELETE FROM users WHERE UserID = :id");
        return $stmt->execute([':id' => $id]);
    }
}
