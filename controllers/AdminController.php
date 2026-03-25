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

    /** Attempt admin login */
    public function login(string $username, string $password): bool {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if (!$admin) return false;
        if (!password_verify($password, $admin['password'])) return false;
        // Store admin session
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }

    /** Check if admin is logged in */
    public static function requireAuth(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['admin_id'])) {
            header('Location: /admin/login.php');
            exit;
        }
    }

    /** Logout admin */
    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['admin_id'], $_SESSION['admin_username']);
        session_destroy();
        header('Location: /admin/login.php');
        exit;
    }

    /** Get admin by ID */
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT id,username,email FROM admins WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: false;
    }
}