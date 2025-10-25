<?php
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Quick Setup</title>";
echo "<style>body{font-family:Arial;max-width:800px;margin:40px auto;padding:20px;background:#f5f5f5;}";
echo ".status{padding:15px;margin:10px 0;border-radius:4px;font-weight:bold;}";
echo ".success{background:#d4edda;color:#155724;}.error{background:#f8d7da;color:#721c24;}";
echo "</style></head><body><h1>🔧 Database Setup</h1>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Create bookings table
    $sql1 = "CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) UNIQUE NOT NULL,
        borrower_name VARCHAR(255) NOT NULL,
        identity VARCHAR(100) NOT NULL,
        unit VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL,
        facility_name VARCHAR(255) NOT NULL,
        facility_id INT,
        event_name VARCHAR(255) NOT NULL,
        participant_count INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        purpose TEXT NOT NULL,
        document_name VARCHAR(255) DEFAULT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_booking_id (booking_id),
        INDEX idx_status (status),
        INDEX idx_facility (facility_id),
        INDEX idx_dates (start_date, end_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql1);
    echo "<div class='status success'>✅ Tabel bookings berhasil dibuat!</div>";
    
    // Create booking_facilities junction table
    $sql2 = "CREATE TABLE IF NOT EXISTS booking_facilities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) NOT NULL,
        facility_id INT NOT NULL,
        facility_name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_booking (booking_id),
        INDEX idx_facility (facility_id),
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql2);
    echo "<div class='status success'>✅ Tabel booking_facilities berhasil dibuat!</div>";
    
    // Check existing data
    $stmt = $conn->query("SELECT COUNT(*) as count FROM bookings");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<div class='status success'>📊 Database siap! Jumlah booking: <strong>$count</strong></div>";
    
    echo "<h3>✅ Setup Selesai!</h3>";
    echo "<p>Sekarang Anda bisa:</p>";
    echo "<ol>";
    echo "<li><a href='http://localhost/peminjaman/borrow.html'>Buat peminjaman baru</a></li>";
    echo "<li><a href='http://localhost/peminjaman/admin.html'>Buka admin panel</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div class='status error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
