<?php
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Fix Table</title>";
echo "<style>body{font-family:Arial;max-width:800px;margin:40px auto;padding:20px;background:#f5f5f5;}";
echo ".status{padding:15px;margin:10px 0;border-radius:4px;font-weight:bold;}";
echo ".success{background:#d4edda;color:#155724;}.error{background:#f8d7da;color:#721c24;}";
echo "</style></head><body><h1>🔧 Fix Table Structure</h1>";

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Drop existing tables
    echo "<p>Dropping existing tables...</p>";
    $conn->exec("DROP TABLE IF EXISTS booking_facilities");
    $conn->exec("DROP TABLE IF EXISTS bookings");
    echo "<div class='status success'>✅ Old tables dropped</div>";
    
    // Create bookings table with flexible structure
    $sql1 = "CREATE TABLE bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) UNIQUE NOT NULL,
        borrower_name VARCHAR(255) NOT NULL,
        identity VARCHAR(100) DEFAULT NULL,
        unit VARCHAR(255) DEFAULT NULL,
        phone VARCHAR(20) DEFAULT NULL,
        email VARCHAR(255) DEFAULT NULL,
        facility_name VARCHAR(255) NOT NULL,
        facility_id INT DEFAULT NULL,
        room_type VARCHAR(100) DEFAULT NULL,
        event_name VARCHAR(255) DEFAULT NULL,
        participant_count INT DEFAULT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        start_time TIME DEFAULT NULL,
        end_time TIME DEFAULT NULL,
        purpose TEXT DEFAULT NULL,
        notes TEXT DEFAULT NULL,
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
    echo "<div class='status success'>✅ Tabel bookings berhasil dibuat dengan struktur baru!</div>";
    
    // Create booking_facilities junction table
    $sql2 = "CREATE TABLE booking_facilities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        booking_id VARCHAR(50) NOT NULL,
        facility_id INT DEFAULT NULL,
        facility_name VARCHAR(255) NOT NULL,
        item VARCHAR(255) NOT NULL,
        quantity INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_booking (booking_id),
        INDEX idx_facility (facility_id),
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->exec($sql2);
    echo "<div class='status success'>✅ Tabel booking_facilities berhasil dibuat!</div>";
    
    echo "<h3>✅ Fix Selesai!</h3>";
    echo "<p>Struktur tabel sudah diperbaiki. Field yang tidak wajib sekarang bisa NULL.</p>";
    echo "<p><strong>Silakan test lagi:</strong></p>";
    echo "<ol>";
    echo "<li><a href='http://localhost/peminjaman/borrow.html'>Isi form peminjaman</a></li>";
    echo "<li><a href='http://localhost/peminjaman/admin.html'>Buka admin panel</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div class='status error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
