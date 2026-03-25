<?php
/**
 * Watch Showcase - Configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'watch_showcase');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application
define('SITE_NAME', 'CHRONOS — Premium Watch Gallery');
define('SITE_TAGLINE', 'นาฬิกาเครื่องใหญ่ระดับพรีเมียม');

// Auto-detect BASE_URL from script path
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Walk up if we are inside /admin or /admin/api
if (preg_match('#/admin(/api)?$#', $scriptDir)) {
    $scriptDir = preg_replace('#/admin(/api)?$#', '', $scriptDir);
}
define('BASE_URL', rtrim($scriptDir, '/') . '/');

// Upload
define('UPLOAD_DIR', __DIR__ . '/uploads/products/');
define('UPLOAD_URL', BASE_URL . 'uploads/products/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Create upload dir if not exists
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
