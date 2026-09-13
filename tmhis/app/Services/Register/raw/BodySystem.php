<?php
// models/BodySystem.php

require_once __DIR__ . '/../config/Database.php';

class BodySystem
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM body_systems WHERE Status = 'Active' ORDER BY BodySystemID ASC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM body_systems WHERE BodySystemID = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM body_systems WHERE SystemCode = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
