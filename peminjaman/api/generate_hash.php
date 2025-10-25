<?php
/**
 * Helper Script untuk Generate Password Hash
 * Jalankan: php generate_hash.php
 */

$passwords = [
    'admin123' => null,
    'staff123' => null,
    'user123' => null,
];

echo "Generating password hashes...\n\n";

foreach ($passwords as $password => $hash) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    echo "Password: {$password}\n";
    echo "Hash: {$hash}\n";
    echo "---\n\n";
}

echo "Copy hash di atas dan gunakan untuk update tabel users di database.\n";
echo "\nContoh SQL:\n";
echo "UPDATE users SET password = '<hash>' WHERE email = 'admin@kampus.ac.id';\n";
