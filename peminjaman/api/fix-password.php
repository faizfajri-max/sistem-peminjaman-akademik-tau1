<?php
/**
 * FIX PASSWORD - Direct Update via PHP
 */

require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>🔧 Fix Password - Direct Update</h2>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;} .box{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);margin:10px 0;} .success{color:green;} .error{color:red;} code{background:#f4f4f4;padding:2px 6px;border-radius:3px;}</style>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Generate new hash
    $password = 'admin123';
    $newHash = password_hash($password, PASSWORD_BCRYPT);
    
    echo "<div class='box'>";
    echo "<h3>Step 1: Generate New Hash</h3>";
    echo "<p><strong>Password:</strong> <code>{$password}</code></p>";
    echo "<p><strong>New Hash:</strong></p>";
    echo "<p><code style='display:block;padding:10px;background:#f9f9f9;word-break:break-all;'>{$newHash}</code></p>";
    echo "</div>";
    
    // Check if we should update
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'update') {
        echo "<div class='box'>";
        echo "<h3>Step 2: Updating Database...</h3>";
        
        // Update all 3 default users
        $emails = ['admin@kampus.ac.id', 'staff@kampus.ac.id', 'user@kampus.ac.id'];
        $updateCount = 0;
        
        foreach ($emails as $email) {
            $query = "UPDATE users SET password = :password WHERE email = :email";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':password', $newHash);
            $stmt->bindParam(':email', $email);
            
            if ($stmt->execute()) {
                echo "<p class='success'>✅ Updated: {$email}</p>";
                $updateCount++;
            } else {
                echo "<p class='error'>❌ Failed: {$email}</p>";
            }
        }
        
        echo "<p><strong class='success'>Total updated: {$updateCount} users</strong></p>";
        echo "</div>";
        
        // Verify
        echo "<div class='box'>";
        echo "<h3>Step 3: Verification</h3>";
        
        $query = "SELECT id, email, password FROM users WHERE email IN ('admin@kampus.ac.id', 'staff@kampus.ac.id', 'user@kampus.ac.id')";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table style='width:100%;border-collapse:collapse;'>";
        echo "<tr style='background:#4CAF50;color:white;'><th style='padding:8px;border:1px solid #ddd;'>Email</th><th style='padding:8px;border:1px solid #ddd;'>Password Test</th><th style='padding:8px;border:1px solid #ddd;'>Status</th></tr>";
        
        foreach ($users as $user) {
            $verify = password_verify($password, $user['password']);
            $status = $verify ? '<span class="success">✅ VALID</span>' : '<span class="error">❌ INVALID</span>';
            
            echo "<tr>";
            echo "<td style='padding:8px;border:1px solid #ddd;'>{$user['email']}</td>";
            echo "<td style='padding:8px;border:1px solid #ddd;'><code>admin123</code></td>";
            echo "<td style='padding:8px;border:1px solid #ddd;'>{$status}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        
        echo "<div class='box' style='background:#d4edda;border:1px solid #c3e6cb;'>";
        echo "<h3 class='success'>✅ Password Updated Successfully!</h3>";
        echo "<p><strong>You can now login with:</strong></p>";
        echo "<ul>";
        echo "<li>Email: <code>admin@kampus.ac.id</code> / Password: <code>admin123</code></li>";
        echo "<li>Email: <code>staff@kampus.ac.id</code> / Password: <code>admin123</code></li>";
        echo "<li>Email: <code>user@kampus.ac.id</code> / Password: <code>admin123</code></li>";
        echo "</ul>";
        echo "<p><a href='http://localhost/peminjaman/admin-login.html' style='display:inline-block;background:#4CAF50;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;margin-top:10px;'>Go to Login Page</a></p>";
        echo "</div>";
        
    } else {
        echo "<div class='box'>";
        echo "<h3>Ready to Update?</h3>";
        echo "<p>Click the button below to update password for all 3 default users:</p>";
        echo "<p><a href='?action=update' style='display:inline-block;background:#FF5722;color:white;padding:15px 30px;text-decoration:none;border-radius:5px;font-weight:bold;'>UPDATE PASSWORD NOW</a></p>";
        echo "<p style='color:#666;font-size:14px;margin-top:20px;'>This will update password for:<br>";
        echo "- admin@kampus.ac.id<br>";
        echo "- staff@kampus.ac.id<br>";
        echo "- user@kampus.ac.id</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='box' style='background:#f8d7da;border:1px solid #f5c6cb;'>";
    echo "<h3 class='error'>❌ Error: " . $e->getMessage() . "</h3>";
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>Database <code>peminjaman_db</code> exists</li>";
    echo "<li>XAMPP MySQL is running</li>";
    echo "<li>Database config in <code>config/database.php</code> is correct</li>";
    echo "</ul>";
    echo "</div>";
}
?>
