<?php
/**
 * General Configuration
 * Konfigurasi umum aplikasi
 */

// JWT Secret Key (gunakan string yang lebih kuat di production)
define('JWT_SECRET', 'your-secret-key-change-this-in-production');
define('JWT_ALGORITHM', 'HS256');

// CORS Settings
define('CORS_ORIGIN', '*'); // Ganti dengan domain frontend Anda di production

// Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Timezone
date_default_timezone_set('Asia/Jakarta');
