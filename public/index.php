<?php

/**
 * ============================================================
 * Strip Map Ruas Jalan — Entry Point
 * ============================================================
 * Semua request masuk melalui file ini.
 */

// Definisikan base path aplikasi
define('BASE_PATH', dirname(__DIR__));

// ── Load file .env ke $_ENV ──────────────────────────────────
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Lewati komentar dan baris tanpa '='
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\"'"); // hapus spasi & quote opsional
        if ($key !== '' && !array_key_exists($key, $_ENV)) {
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Mulai session
session_start();

// Nonaktifkan cache agar LiteSpeed/browser tidak menyajikan halaman yang sama untuk semua URL
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

// Muat konfigurasi
$appConfig = require BASE_PATH . '/config/app.php';

// Error reporting (aktifkan jika debug = true)
if ($appConfig['debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Set timezone
date_default_timezone_set($appConfig['timezone']);

// Muat autoloader
require_once BASE_PATH . '/app/helpers/Autoloader.php';

// Muat helper functions
require_once BASE_PATH . '/app/helpers/functions.php';

// Muat Router class
require_once BASE_PATH . '/app/helpers/Router.php';

// Muat Database class
require_once BASE_PATH . '/app/helpers/Database.php';

// ── Validasi koneksi database saat startup ───────────────────
// Database::getInstance() akan throw RuntimeException jika koneksi gagal.
// Kita tangkap di sini dan render view error yang bersih.
try {
    Database::getInstance();
} catch (\RuntimeException $e) {
    http_response_code(500);
    $errorMessage = $appConfig['debug'] ? $e->getMessage() : null;
    require BASE_PATH . '/resources/views/errors/db_error.php';
    exit;
}

// Muat dan jalankan router
require_once BASE_PATH . '/routes/web.php';

