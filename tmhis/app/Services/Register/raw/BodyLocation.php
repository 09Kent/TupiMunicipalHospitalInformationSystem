<?php
// models/BodyLocation.php

require_once __DIR__ . '/../config/Database.php';

class BodyLocation
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT bl.*, bs.SystemName, bs.SystemCode, bs.Emoji AS SystemEmoji
            FROM body_locations bl
            LEFT JOIN body_systems bs ON bs.BodySystemID = bl.BodySystemID
            ORDER BY bl.BodyLocationID ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT bl.*, bs.SystemName, bs.SystemCode, bs.Emoji AS SystemEmoji
            FROM body_locations bl
            LEFT JOIN body_systems bs ON bs.BodySystemID = bl.BodySystemID
            WHERE bl.BodyLocationID = :id LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByCode(string $code): ?array
    {
        $stmt = $this->db->prepare("
            SELECT bl.*, bs.SystemName, bs.SystemCode, bs.Emoji AS SystemEmoji
            FROM body_locations bl
            LEFT JOIN body_systems bs ON bs.BodySystemID = bl.BodySystemID
            WHERE bl.LocationCode = :code LIMIT 1
        ");
        $stmt->execute(['code' => $code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
