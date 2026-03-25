<?php
// ============================================
// DINELOCAL · controllers/AdminController.php
// Admin authentication controller (MVC)
// ITC 6355 | Arjun & Ayomide
// ============================================
require_once __DIR__ . '/../config/db.php';

class AdminController {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    /** Attempt admin login — accepts username or email */
    public function login(string $username, string $password): bool {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $admin = $stmt->fetch();
        if (!$admin) return false;
        if (!password_verify($password, $admin['password'])) return false;
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_role']     = $admin['role'];
        return true;
    }

    /** Require admin to be logged in (blocks users too) */
    public static function requireAuth(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Block regular users from admin area
        if (!empty($_SESSION['user_id']) && empty($_SESSION['admin_id'])) {
            header('Location: ../dashboard.php');
            exit;
        }
        if (empty($_SESSION['admin_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    /** Require a specific role — call after requireAuth() */
    public static function requireRole(string ...$roles): void {
        $current = $_SESSION['admin_role'] ?? '';
        if (!in_array($current, $roles)) {
            header('Location: index.php?denied=1');
            exit;
        }
    }

    /** Check if current admin has a role */
    public static function hasRole(string ...$roles): bool {
        return in_array($_SESSION['admin_role'] ?? '', $roles);
    }

    /** Logout admin */
    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }

    /** Get admin by ID */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT id,username,email,role FROM admins WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: false;
    }

    /** Get all admins */
    public function getAll(): array {
        return $this->db->query("SELECT id,username,email,role,created_at FROM admins ORDER BY created_at ASC")->fetchAll();
    }

    /** Create new admin */
    public function create(string $username, string $email, string $password, string $role): bool {
        $stmt = $this->db->prepare("INSERT INTO admins (username,email,password,role) VALUES (?,?,?,?)");
        return $stmt->execute([$username, $email, password_hash($password, PASSWORD_BCRYPT), $role]);
    }

    /** Update admin role */
    public function updateRole(int $id, string $role): bool {
        return $this->db->prepare("UPDATE admins SET role=? WHERE id=?")->execute([$role, $id]);
    }

    /** Reset admin password */
    public function resetPassword(int $id, string $newPassword): bool {
        return $this->db->prepare("UPDATE admins SET password=? WHERE id=?")
                        ->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
    }

    /** Delete admin */
    public function delete(int $id): bool {
        return $this->db->prepare("DELETE FROM admins WHERE id=?")->execute([$id]);
    }
}
