<?php
/**
 * Generate Password Hash untuk Testing
 * 
 * Gunakan file ini untuk generate password hash yang benar
 * Akses via: http://localhost/peminjaman/api/generate-password.php
 * 
 * ATAU jalankan via CLI:
 * php generate-password.php
 */

// Mode CLI output
if (php_sapi_name() === 'cli') {
    echo "\n=== Password Hash Generator ===\n\n";
    
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    echo "Password: $password\n";
    echo "Hash: $hash\n\n";
    
    echo "SQL Update Query:\n";
    echo "UPDATE users SET password = '$hash' WHERE email = 'admin@kampus.ac.id';\n";
    echo "UPDATE users SET password = '$hash' WHERE email = 'staff@kampus.ac.id';\n";
    echo "UPDATE users SET password = '$hash' WHERE email = 'user@kampus.ac.id';\n\n";
    
    // Test verify
    echo "Test Verify: " . (password_verify($password, $hash) ? 'OK' : 'FAIL') . "\n\n";
    exit;
}

// Mode Web output
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Password Hash</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
        }
        .result {
            background: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #4F46E5;
        }
        .password-item {
            margin: 20px 0;
        }
        .password-item h3 {
            color: #4F46E5;
            margin-bottom: 5px;
        }
        code {
            background: #e5e7eb;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            display: block;
            margin: 5px 0;
            word-break: break-all;
        }
        .info {
            background: #dbeafe;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }
        .success {
            background: #d1fae5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }
        .sql-update {
            background: #1f2937;
            color: #f9fafb;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            margin: 20px 0;
        }
        .sql-update code {
            background: transparent;
            color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Generate Password Hash</h1>
        
        <div class="info">
            <strong>ℹ️ Info:</strong> File ini digunakan untuk generate password hash yang benar untuk user default.
        </div>

        <?php
        // Generate hash untuk password default
        $passwords = [
            'admin123' => password_hash('admin123', PASSWORD_BCRYPT),
            'staff123' => password_hash('staff123', PASSWORD_BCRYPT),
            'user123' => password_hash('user123', PASSWORD_BCRYPT)
        ];
        ?>

        <h2>📋 Password Hash yang Dihasilkan:</h2>

        <div class="password-item">
            <h3>Admin</h3>
            <div class="result">
                <strong>Email:</strong> admin@kampus.ac.id<br>
                <strong>Password:</strong> admin123<br>
                <strong>Hash:</strong><br>
                <code><?php echo $passwords['admin123']; ?></code>
            </div>
        </div>

        <div class="password-item">
            <h3>Staff</h3>
            <div class="result">
                <strong>Email:</strong> staff@kampus.ac.id<br>
                <strong>Password:</strong> staff123<br>
                <strong>Hash:</strong><br>
                <code><?php echo $passwords['staff123']; ?></code>
            </div>
        </div>

        <div class="password-item">
            <h3>User</h3>
            <div class="result">
                <strong>Email:</strong> user@kampus.ac.id<br>
                <strong>Password:</strong> user123<br>
                <strong>Hash:</strong><br>
                <code><?php echo $passwords['user123']; ?></code>
            </div>
        </div>

        <h2>🗄️ SQL Update Query:</h2>
        <div class="sql-update">
            <code>-- Update password users dengan hash yang benar
UPDATE users SET password = '<?php echo $passwords['admin123']; ?>' WHERE email = 'admin@kampus.ac.id';
UPDATE users SET password = '<?php echo $passwords['staff123']; ?>' WHERE email = 'staff@kampus.ac.id';
UPDATE users SET password = '<?php echo $passwords['user123']; ?>' WHERE email = 'user@kampus.ac.id';</code>
        </div>

        <div class="success">
            <strong>✅ Cara Menggunakan:</strong><br>
            1. Copy SQL query di atas<br>
            2. Buka phpMyAdmin (http://localhost/phpmyadmin)<br>
            3. Pilih database: <code>peminjaman_db</code><br>
            4. Klik tab "SQL"<br>
            5. Paste query dan klik "Go"<br>
            6. Selesai! Sekarang bisa login dengan password yang benar
        </div>

        <h2>🧪 Test Verify:</h2>
        <?php
        // Test verify password
        $testPassword = 'admin123';
        $testHash = $passwords['admin123'];
        $verify = password_verify($testPassword, $testHash);
        ?>
        <div class="result">
            <strong>Test Password:</strong> <?php echo $testPassword; ?><br>
            <strong>Hasil Verify:</strong> <?php echo $verify ? '✅ VALID' : '❌ INVALID'; ?>
        </div>

        <div class="info" style="margin-top: 30px;">
            <strong>📝 Catatan:</strong><br>
            - Hash password menggunakan BCRYPT algorithm<br>
            - Setiap kali generate akan menghasilkan hash berbeda (karena salt random)<br>
            - Hash tetap valid untuk memverifikasi password yang sama<br>
            - Jangan share hash password ke publik
        </div>
    </div>
</body>
</html>
