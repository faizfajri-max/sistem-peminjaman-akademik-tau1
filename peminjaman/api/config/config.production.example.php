<?php
/**
 * EXAMPLE Production Configuration
 * Copy ke config.php dan sesuaikan dengan environment production
 */

// JWT Secret - GANTI dengan string yang kuat dan unik!
define('JWT_SECRET', 'PRODUCTION-SECRET-KEY-CHANGE-THIS-TO-STRONG-RANDOM-STRING-32-CHARS-OR-MORE');
define('JWT_ALGORITHM', 'HS256');

// CORS Settings - Ganti dengan domain frontend production
define('CORS_ORIGIN', 'https://yourdomain.com');
// Atau jika multiple domains:
// define('CORS_ORIGIN', 'https://yourdomain.com, https://app.yourdomain.com');

// Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting (disable di production)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

// Session Settings (jika diperlukan)
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Enable jika menggunakan HTTPS
ini_set('session.cookie_samesite', 'Strict');

// Security Headers (tambahan, bisa diset di .htaccess juga)
// header('X-Frame-Options: DENY');
// header('X-Content-Type-Options: nosniff');
// header('X-XSS-Protection: 1; mode=block');
// header('Referrer-Policy: strict-origin-when-cross-origin');

// Database Connection Pool (advanced, requires mysqlnd)
// ini_set('mysqli.allow_local_infile', 0);
// ini_set('mysqli.allow_persistent', 1);
// ini_set('mysqli.max_persistent', 10);

// API Rate Limiting (implement di index.php jika diperlukan)
// define('RATE_LIMIT_REQUESTS', 100);
// define('RATE_LIMIT_PERIOD', 3600); // per hour

// Maintenance Mode
// define('MAINTENANCE_MODE', false);
// define('MAINTENANCE_IPS', ['127.0.0.1', 'your-ip']); // IPs yang bisa akses saat maintenance

// Email Configuration (jika ada fitur email)
// define('SMTP_HOST', 'smtp.gmail.com');
// define('SMTP_PORT', 587);
// define('SMTP_USER', 'your-email@gmail.com');
// define('SMTP_PASS', 'your-app-password');
// define('SMTP_FROM', 'noreply@yourdomain.com');
// define('SMTP_FROM_NAME', 'Sistem Peminjaman Fasilitas');
