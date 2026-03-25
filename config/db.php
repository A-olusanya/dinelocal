<?php
// ============================================
// DINELOCAL · config/db.php
// ITC 6355 | Arjun & Ayomide
// NOTE: Use 127.0.0.1 NOT localhost on Mac
// localhost uses a Unix socket that may not
// exist; 127.0.0.1 forces TCP connection
// ============================================

// Railway injects MYSQL_* env vars automatically when MySQL plugin is added.
// Falls back to local dev values when running on localhost.
define('DB_HOST',    getenv('MYSQL_HOST')     ?: getenv('MYSQLHOST')     ?: '127.0.0.1');
define('DB_PORT',    getenv('MYSQL_PORT')     ?: getenv('MYSQLPORT')     ?: '3306');
define('DB_NAME',    getenv('MYSQL_DATABASE') ?: getenv('MYSQLDATABASE') ?: 'dinelocal');
define('DB_USER',    getenv('MYSQL_USER')     ?: getenv('MYSQLUSER')     ?: 'root');
define('DB_PASS',    getenv('MYSQL_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: '');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed. Please try again later.']));
        }
    }
    return $pdo;
}