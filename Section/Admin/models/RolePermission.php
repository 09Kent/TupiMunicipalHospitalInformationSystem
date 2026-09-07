<?php
// Section/Admin/models/RolePermission.php

require_once __DIR__ . '/../config/Database.php';

class RolePermission
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getRoles(): array
    {
        $stmt = $this->db->query("SELECT * FROM roles ORDER BY RoleID ASC");
        return $stmt->fetchAll();
    }

    public function createRole(array $data): int
    {
        $sql = "INSERT INTO roles (RoleCode, RoleName, Description, IsSystemRole)
                VALUES (:RoleCode, :RoleName, :Description, 0)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':RoleCode' => strtoupper(trim($data['RoleCode'])),
            ':RoleName' => $data['RoleName'],
            ':Description' => $data['Description'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateRole(int $id, array $data): bool
    {
        $sql = "UPDATE roles SET
                RoleName = :RoleName,
                Description = :Description
                WHERE RoleID = :id AND IsSystemRole = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':RoleName' => $data['RoleName'],
            ':Description' => $data['Description'] ?? null
        ]);
    }

    public function deleteRole(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM roles WHERE RoleID = :id AND IsSystemRole = 0");
        return $stmt->execute([':id' => $id]);
    }
}
