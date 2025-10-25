<?php
/**
 * EXAMPLE Production Database Configuration
 * Copy ke database.php dan sesuaikan dengan server production
 */

class Database {
    // Production Database Settings
    private $host = "localhost";              // atau IP database server
    private $db_name = "peminjaman_db";       // nama database production
    private $username = "db_username";         // username database (JANGAN gunakan root!)
    private $password = "strong_password";     // password yang kuat
    private $charset = "utf8mb4";
    
    public $conn;

    /**
     * Mendapatkan koneksi database
     */
    public function getConnection() {
        $this->conn = null;

        try {
            // Production connection with additional options
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false, // Set true jika butuh persistent connection
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            // Jangan tampilkan detail error di production
            error_log("Database Connection Error: " . $e->getMessage());
            
            // Return generic error ke client
            http_response_code(503);
            echo json_encode([
                'success' => false,
                'error' => 'Service temporarily unavailable. Please try again later.'
            ]);
            exit;
        }

        return $this->conn;
    }
    
    /**
     * Close connection (optional, PDO auto-close)
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
