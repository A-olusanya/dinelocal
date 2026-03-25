<?php
// ============================================
// DINELOCAL · models/User.php
// User model — registration, login, profile
// ITC 6355 | Arjun & Ayomide
// ============================================
require_once __DIR__ . '/../config/db.php';

class User {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    /** Register new user */
    public function register(array $data): int|false {
        // Check email not taken
        if ($this->findByEmail($data['email'])) return false;
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, phone, dietary)
            VALUES (:name, :email, :password, :phone, :dietary)
        ");
        $stmt->execute([
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':phone'    => $data['phone']   ?? null,
            ':dietary'  => $data['dietary'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Verify login credentials */
    public function login(string $email, string $password): array|false {
        $user = $this->findByEmail($email);
        if (!$user) return false;
        if (!password_verify($password, $user['password'])) return false;
        return $user;
    }

    /** Find user by email */
    public function findByEmail(string $email): array|false {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: false;
    }

    /** Get user by ID */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: false;
    }

    /** Update profile */
    public function updateProfile(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE users SET name=:name, phone=:phone, dietary=:dietary WHERE id=:id
        ");
        return $stmt->execute([':id'=>$id,':name'=>$data['name'],':phone'=>$data['phone']??null,':dietary'=>$data['dietary']??null]);
    }

    /** Change password */
    public function changePassword(int $id, string $newPassword): bool {
        $stmt = $this->db->prepare("UPDATE users SET password=? WHERE id=?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }

    /** Get user's reservations */
    public function getReservations(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM reservations WHERE user_id=? ORDER BY date DESC, time ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Get all users (admin) */
    public function getAll(): array {
        return $this->db->query("SELECT id,name,email,phone,dietary,force_password_change,created_at FROM users ORDER BY created_at DESC")->fetchAll();
    }

    /** Create a password reset request */
    public function createResetRequest(string $email): bool {
        $user = $this->findByEmail($email);
        if (!$user) return false;
        $stmt = $this->db->prepare("
            INSERT INTO password_reset_requests (user_id, email, status)
            VALUES (?, ?, 'pending')
        ");
        return $stmt->execute([$user['id'], $email]);
    }

    /** Get all pending reset requests (admin) */
    public function getPendingResets(): array {
        return $this->db->query("
            SELECT r.*, u.name
            FROM password_reset_requests r
            LEFT JOIN users u ON u.id = r.user_id
            WHERE r.status = 'pending'
            ORDER BY r.requested_at ASC
        ")->fetchAll();
    }

    /** Admin sets a temporary password for a user */
    public function setTempPassword(int $userId, string $tempPassword, int $requestId): bool {
        $hash = password_hash($tempPassword, PASSWORD_BCRYPT);
        $this->db->prepare("UPDATE users SET password=?, force_password_change=1 WHERE id=?")
                 ->execute([$hash, $userId]);
        $this->db->prepare("UPDATE password_reset_requests SET status='resolved', temp_password=?, resolved_at=NOW() WHERE id=?")
                 ->execute([$hash, $requestId]);
        return true;
    }

    /** Clear forced password change flag after user updates password */
    public function clearForceChange(int $id): bool {
        return $this->db->prepare("UPDATE users SET force_password_change=0 WHERE id=?")
                        ->execute([$id]);
    }
}