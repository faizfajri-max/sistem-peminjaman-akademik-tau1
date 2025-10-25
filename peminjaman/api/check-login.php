<?php
/**
 * Debug Login - Check Password Hash
 */

require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get users
    $query = "SELECT id, name, email, password, role FROM users WHERE email IN ('admin@kampus.ac.id', 'staff@kampus.ac.id', 'user@kampus.ac.id')";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>🔍 Debug Login - Password Check</h2>";
    echo "<style>body{font-family:Arial;padding:20px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#4CAF50;color:white;} .pass{color:green;} .fail{color:red;}</style>";
    
    echo "<h3>Users in Database:</h3>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Password Hash (first 50 chars)</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['name']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td><code>" . substr($user['password'], 0, 50) . "...</code></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test password verification
    echo "<h3>Password Verification Test:</h3>";
    echo "<table>";
    echo "<tr><th>Email</th><th>Password Input</th><th>Verify Result</th><th>Status</th></tr>";
    
    $testPassword = 'admin123';
    
    foreach ($users as $user) {
        $verify = password_verify($testPassword, $user['password']);
        $status = $verify ? '<span class="pass">✅ VALID</span>' : '<span class="fail">❌ INVALID</span>';
        
        echo "<tr>";
        echo "<td>{$user['email']}</td>";
        echo "<td><code>admin123</code></td>";
        echo "<td>" . ($verify ? 'TRUE' : 'FALSE') . "</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Generate new hash
    $newHash = password_hash('admin123', PASSWORD_BCRYPT);
    echo "<h3>Solution:</h3>";
    echo "<p><strong>New Hash Generated:</strong></p>";
    echo "<p><code style='background:#f4f4f4;padding:10px;display:block;'>{$newHash}</code></p>";
    
    echo "<h4>Run this SQL to fix:</h4>";
    echo "<pre style='background:#f4f4f4;padding:15px;'>";
    echo "UPDATE users \n";
    echo "SET password = '{$newHash}' \n";
    echo "WHERE email IN ('admin@kampus.ac.id', 'staff@kampus.ac.id', 'user@kampus.ac.id');\n";
    echo "</pre>";
    
    echo "<p><a href='http://localhost/phpmyadmin' target='_blank' style='background:#4CAF50;color:white;padding:10px 20px;text-decoration:none;display:inline-block;margin-top:10px;'>Open phpMyAdmin</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error: " . $e->getMessage() . "</h3>";
    echo "<p>Make sure database exists and config is correct.</p>";
}
?>
