<?php
// ============================================
// DINELOCAL · models/Reservation.php
// Reservation model — CRUD (MVC: Model)
// ITC 6355 | Arjun & Ayomide
// ============================================
require_once __DIR__ . '/../config/db.php';

class Reservation {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    /** Get all reservations ordered by date */
    public function getAll(): array {
        return $this->db->query("SELECT * FROM reservations ORDER BY date DESC, time ASC")->fetchAll();
    }

    /** Get reservations by status */
    public function getByStatus(string $status): array {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE status = ? ORDER BY date DESC");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    /** Get single reservation by ID */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Create new reservation */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO reservations (full_name, email, guests, date, time, special)
            VALUES (:full_name, :email, :guests, :date, :time, :special)
        ");
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':email'     => $data['email'],
            ':guests'    => $data['guests'],
            ':date'      => $data['date'],
            ':time'      => $data['time'],
            ':special'   => $data['special'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Update reservation status */
    public function updateStatus(int $id, string $status): bool {
        $allowed = ['pending', 'confirmed', 'cancelled'];
        if (!in_array($status, $allowed)) return false;
        $stmt = $this->db->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /** Delete reservation */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Count reservations by status (for admin dashboard) */
    public function countByStatus(): array {
        $rows = $this->db->query("SELECT status, COUNT(*) as total FROM reservations GROUP BY status")->fetchAll();
        $result = ['pending' => 0, 'confirmed' => 0, 'cancelled' => 0];
        foreach ($rows as $row) $result[$row['status']] = (int)$row['total'];
        return $result;
    }

    /** Get today's reservations */
    public function getToday(): array {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE date = CURDATE() ORDER BY time ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}