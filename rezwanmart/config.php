<?php
// ============================================
// RezwanMart — Database Configuration
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Laragon default
define('DB_PASS', '');           // Laragon default (empty)
define('DB_NAME', 'rezwanmart');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("<h3 style='color:red; font-family:sans-serif; padding:20px;'>
        ❌ Database connection failed: " . mysqli_connect_error() . "
        <br><small>Make sure Laragon is running and database is imported.</small>
    </h3>");
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Site Configuration
define('SITE_NAME', 'RezwanMart');
define('SITE_URL', 'http://localhost/rezwanmart');
define('UPLOAD_PATH', __DIR__ . '/uploads/');
?>
