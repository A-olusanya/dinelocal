<?php
// ============================================================
// DINELOCAL · config/db.php
// ITC 6355 | Arjun & Ayomide
// Azure App Service reads env vars from
// Configuration > Application Settings.
// Falls back to local dev values.
// ============================================================

define('DB_HOST',    getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT',    getenv('DB_PORT') ?: '3306');
define('DB_NAME',    getenv('DB_NAME') ?: 'dinelocal');
define('DB_USER',    getenv('DB_USER') ?: 'root');
define('DB_PASS',    getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );

    // ── PDO options explained ───────────────────────────────
    //
    // ATTR_PERSISTENT => false
    //   Azure App Service Linux runs PHP-FPM. Each FPM worker is a
    //   long-lived process, so `static $pdo` already reuses the
    //   connection for every request handled by that worker — this
    //   is "persistent" enough without the extra complexity of
    //   PDO persistent connections (which can leave transactions
    //   open across requests and cause hard-to-debug bugs).
    //
    // MYSQL_ATTR_INIT_COMMAND
    //   Runs immediately after connect. Sets:
    //   - time_zone: avoids a round-trip for timezone negotiation.
    //   - wait_timeout: if the DB is cross-region, Azure may close
    //     idle connections after 8 hours (MySQL default). This
    //     reduces that to 28800 s which matches MySQL's default but
    //     makes it explicit. Lower to e.g. 300 if you see
    //     "MySQL server has gone away" errors.
    //   - net_read_timeout / net_write_timeout: increase read/write
    //     deadlines for high-latency cross-region links. Default is
    //     30 s which can be too tight when the DB is in a different
    //     Azure region.
    //
    // MYSQL_ATTR_COMPRESS => true
    //   Enables zlib compression on the wire between PHP and MySQL.
    //   On cross-region links (e.g. App Service in Canada Central,
    //   Azure Database for MySQL in East US) this cuts data transfer
    //   time for larger result sets. Negligible overhead on the CPU.
    //
    // MYSQL_ATTR_SSL_CA
    //   Azure Database for MySQL Flexible Server requires SSL.
    //   The DigiCert root CA bundle is bundled with PHP on Azure
    //   App Service at this path. Set MYSQL_SSL=true in App Settings
    //   to opt in without changing code in dev (where there is no
    //   CA file).
    //
    // ATTR_EMULATE_PREPARES => false
    //   Use true server-side prepared statements. Safer and slightly
    //   faster for repeated queries because the query plan is cached
    //   on the DB side.
    //
    // ATTR_STRINGIFY_FETCHES => false
    //   Return native PHP types (int, float) instead of strings for
    //   numeric columns. Saves type-casting throughout the app.

    $options = [
        PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES     => false,
        PDO::ATTR_STRINGIFY_FETCHES    => false,
        PDO::ATTR_PERSISTENT           => false,

        // Tune MySQL session variables on connect
        PDO::MYSQL_ATTR_INIT_COMMAND   =>
            "SET time_zone='+00:00'," .
            "wait_timeout=28800," .
            "net_read_timeout=60," .
            "net_write_timeout=60",

        // Wire compression for cross-region latency (safe to keep on
        // even for same-region; minimal CPU cost, real benefit at
        // distance).
        PDO::MYSQL_ATTR_COMPRESS       => true,
    ];

    // Add SSL only when explicitly enabled via App Settings.
    // On Azure Database for MySQL Flexible Server, SSL is mandatory.
    // Set MYSQL_SSL=true and optionally MYSQL_SSL_CA to the CA path.
    if (getenv('MYSQL_SSL') === 'true') {
        $caPath = getenv('MYSQL_SSL_CA')
            ?: '/var/ssl/certs/DigiCertGlobalRootCA.crt.pem';
        if (file_exists($caPath)) {
            $options[PDO::MYSQL_ATTR_SSL_CA]         = $caPath;
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }
    }

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // Never expose connection details; log the real error.
        error_log('DB connection failed: ' . $e->getMessage());
        http_response_code(503);
        die(json_encode(['error' => 'Database connection failed. Please try again later.']));
    }

    return $pdo;
}
