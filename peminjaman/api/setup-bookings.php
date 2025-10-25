<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Bookings Table - Sistem Peminjaman</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
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
            margin-bottom: 20px;
        }
        .status {
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            font-weight: bold;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .btn {
            background: #4F8EF7;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #3d7ae0;
        }
        pre {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #4F8EF7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Setup Bookings Table</h1>
        <p>Tool ini akan membuat tabel <code>bookings</code> dan <code>booking_facilities</code> di database Anda.</p>

        <?php
        require_once __DIR__ . '/config/database.php';

        $messages = [];
        $hasError = false;

        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Check if tables already exist
            $stmt = $conn->query("SHOW TABLES LIKE 'bookings'");
            $bookingsExists = $stmt->rowCount() > 0;

            $stmt = $conn->query("SHOW TABLES LIKE 'booking_facilities'");
            $facilitiesExists = $stmt->rowCount() > 0;

            if ($bookingsExists && $facilitiesExists) {
                echo '<div class="status warning">⚠️ Tabel sudah ada! Tidak perlu membuat lagi.</div>';
                
                // Show existing data
                $stmt = $conn->query("SELECT COUNT(*) as count FROM bookings");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                echo '<div class="status info">📊 Jumlah booking di database: <strong>' . $count . '</strong></div>';
                
                if ($count > 0) {
                    $stmt = $conn->query("SELECT booking_id, borrower_name, facility_name, status, created_at FROM bookings ORDER BY created_at DESC LIMIT 5");
                    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo '<h3>5 Booking Terbaru:</h3>';
                    echo '<table border="1" cellpadding="8" style="width:100%; border-collapse:collapse;">';
                    echo '<tr style="background:#f0f0f0"><th>Kode</th><th>Peminjam</th><th>Fasilitas</th><th>Status</th><th>Dibuat</th></tr>';
                    foreach ($bookings as $b) {
                        $statusColor = $b['status'] === 'approved' ? '#28a745' : ($b['status'] === 'pending' ? '#ffc107' : '#dc3545');
                        echo '<tr>';
                        echo '<td><code>' . htmlspecialchars($b['booking_id']) . '</code></td>';
                        echo '<td>' . htmlspecialchars($b['borrower_name']) . '</td>';
                        echo '<td>' . htmlspecialchars($b['facility_name']) . '</td>';
                        echo '<td style="color:' . $statusColor . '; font-weight:bold">' . $b['status'] . '</td>';
                        echo '<td>' . $b['created_at'] . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            } else {
                // Read SQL file
                $sqlFile = __DIR__ . '/create-bookings-table.sql';
                
                if (!file_exists($sqlFile)) {
                    throw new Exception("File SQL tidak ditemukan: $sqlFile");
                }

                $sql = file_get_contents($sqlFile);
                
                // Execute SQL statements
                $conn->exec("USE peminjaman_db");
                
                // Split by semicolon and execute each statement
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                
                foreach ($statements as $statement) {
                    if (empty($statement) || strpos($statement, '--') === 0) continue;
                    if (strpos($statement, 'SELECT') === 0) continue; // Skip SELECT messages
                    
                    try {
                        $conn->exec($statement);
                    } catch (Exception $e) {
                        // Ignore duplicate errors
                        if (strpos($e->getMessage(), 'already exists') === false) {
                            throw $e;
                        }
                    }
                }

                echo '<div class="status success">✅ Tabel bookings berhasil dibuat!</div>';
                
                // Verify sample data
                $stmt = $conn->query("SELECT COUNT(*) as count FROM bookings");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                
                echo '<div class="status success">✅ Sample data berhasil dimasukkan: <strong>' . $count . ' bookings</strong></div>';
                
                // Show sample data
                $stmt = $conn->query("SELECT booking_id, borrower_name, facility_name, status FROM bookings");
                $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo '<h3>Sample Bookings:</h3>';
                echo '<ul>';
                foreach ($bookings as $b) {
                    echo '<li><code>' . htmlspecialchars($b['booking_id']) . '</code> - ' . 
                         htmlspecialchars($b['borrower_name']) . ' - ' . 
                         htmlspecialchars($b['facility_name']) . ' 
                         (<span style="color:' . ($b['status'] === 'approved' ? 'green' : 'orange') . '">' . $b['status'] . '</span>)</li>';
                }
                echo '</ul>';
            }

        } catch (Exception $e) {
            echo '<div class="status error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            $hasError = true;
        }
        ?>

        <div class="step">
            <h3>📝 Langkah Selanjutnya:</h3>
            <ol>
                <li>Buka <a href="http://localhost/peminjaman/borrow.html" target="_blank">Form Peminjaman</a></li>
                <li>Isi form dan kirim pengajuan peminjaman</li>
                <li>Setelah submit, Anda akan diredirect ke halaman konfirmasi</li>
                <li>Data akan tersimpan di database dan bisa dilihat di halaman konfirmasi</li>
            </ol>
        </div>

        <div class="step">
            <h3>🧪 Test Endpoints:</h3>
            <ul>
                <li><a href="http://localhost/peminjaman/api/bookings" target="_blank">GET /api/bookings</a> - List all bookings</li>
                <li><a href="http://localhost/peminjaman/api/bookings/BK_q1i924j6i" target="_blank">GET /api/bookings/BK_q1i924j6i</a> - Get specific booking</li>
                <li><a href="http://localhost/peminjaman/confirmation.html?id=BK_q1i924j6i" target="_blank">Confirmation Page</a> - Test confirmation page</li>
            </ul>
        </div>

        <div style="margin-top: 30px;">
            <a href="http://localhost/peminjaman/borrow.html" class="btn">📝 Buat Peminjaman Baru</a>
            <a href="http://localhost/peminjaman/confirmation.html" class="btn">✅ Lihat Konfirmasi</a>
        </div>
    </div>
</body>
</html>
