<?php
/**
 * Loans Routes
 * Endpoint untuk peminjaman fasilitas
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/response.php';

class LoansRoutes {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * GET /api/loans
     * List semua peminjaman dengan filter
     */
    public function list() {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $userId = isset($_GET['userId']) ? $_GET['userId'] : null;
        $from = isset($_GET['from']) ? $_GET['from'] : null;
        $to = isset($_GET['to']) ? $_GET['to'] : null;
        $roomType = isset($_GET['roomType']) ? $_GET['roomType'] : null;

        $query = "SELECT l.*, u.name as user_name, u.email as user_email 
                  FROM loans l 
                  LEFT JOIN users u ON l.user_id = u.id 
                  WHERE 1=1";
        
        $params = [];
        
        if ($status) {
            $query .= " AND l.status = :status";
            $params[':status'] = $status;
        }
        
        if ($userId) {
            $query .= " AND l.user_id = :userId";
            $params[':userId'] = $userId;
        }
        
        if ($from) {
            $query .= " AND l.start_date >= :from";
            $params[':from'] = $from;
        }
        
        if ($to) {
            $query .= " AND l.end_date <= :to";
            $params[':to'] = $to;
        }
        
        if ($roomType) {
            $query .= " AND l.room_type = :roomType";
            $params[':roomType'] = $roomType;
        }
        
        $query .= " ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get facilities for each loan
        foreach ($loans as &$loan) {
            $loan['facilities'] = $this->getLoanFacilities($loan['id']);
        }

        Response::json([
            'success' => true,
            'data' => $loans
        ], 200);
    }

    /**
     * GET /api/loans/:id
     * Detail peminjaman
     */
    public function detail($id) {
        $query = "SELECT l.*, u.name as user_name, u.email as user_email 
                  FROM loans l 
                  LEFT JOIN users u ON l.user_id = u.id 
                  WHERE l.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Peminjaman tidak ditemukan', 404);
        }

        $loan = $stmt->fetch(PDO::FETCH_ASSOC);
        $loan['facilities'] = $this->getLoanFacilities($id);

        Response::json([
            'success' => true,
            'data' => $loan
        ], 200);
    }

    /**
     * POST /api/loans
     * Buat peminjaman baru
     */
    public function create() {
        $user = Auth::requireAuth();
        
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        $required = ['unit', 'purpose', 'start_date', 'end_date', 'start_time', 'end_time', 'facilities'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                Response::error("Field $field wajib diisi", 400);
            }
        }

        if (!is_array($data['facilities']) || empty($data['facilities'])) {
            Response::error('Minimal pilih satu fasilitas', 400);
        }

        $unit = trim($data['unit']);
        $purpose = trim($data['purpose']);
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];
        $roomType = isset($data['room_type']) ? $data['room_type'] : null;
        $participants = isset($data['participants']) ? (int)$data['participants'] : 0;
        $notes = isset($data['notes']) ? trim($data['notes']) : '';
        $status = 'pending';

        // Insert loan
        $query = "INSERT INTO loans (user_id, unit, purpose, start_date, end_date, start_time, end_time, 
                  room_type, participants, notes, status, created_at) 
                  VALUES (:user_id, :unit, :purpose, :start_date, :end_date, :start_time, :end_time, 
                  :room_type, :participants, :notes, :status, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user['userId']);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':purpose', $purpose);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);
        $stmt->bindParam(':room_type', $roomType);
        $stmt->bindParam(':participants', $participants);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':status', $status);

        if (!$stmt->execute()) {
            Response::error('Gagal membuat peminjaman', 500);
        }

        $loanId = $this->conn->lastInsertId();

        // Insert loan facilities
        foreach ($data['facilities'] as $facilityId) {
            $query = "INSERT INTO loan_facilities (loan_id, facility_id) VALUES (:loan_id, :facility_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':loan_id', $loanId);
            $stmt->bindParam(':facility_id', $facilityId);
            $stmt->execute();
        }

        Response::json([
            'success' => true,
            'message' => 'Peminjaman berhasil diajukan',
            'data' => [
                'id' => $loanId,
                'status' => $status
            ]
        ], 201);
    }

    /**
     * PATCH /api/loans/:id/status
     * Update status peminjaman (admin/staff only)
     */
    public function updateStatus($id) {
        $user = Auth::requireRole(['admin', 'staff']);
        
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['status'])) {
            Response::error('Status wajib diisi', 400);
        }

        $status = $data['status'];
        $validStatuses = ['pending', 'approved', 'rejected', 'done'];
        
        if (!in_array($status, $validStatuses)) {
            Response::error('Status tidak valid. Pilihan: ' . implode(', ', $validStatuses), 400);
        }

        // Cek apakah loan ada
        $query = "SELECT id FROM loans WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Peminjaman tidak ditemukan', 404);
        }

        // Update status
        $query = "UPDATE loans SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'Status peminjaman berhasil diupdate'
            ], 200);
        } else {
            Response::error('Gagal mengupdate status', 500);
        }
    }

    /**
     * DELETE /api/loans/:id
     * Hapus peminjaman (admin only atau user yang membuat)
     */
    public function delete($id) {
        $user = Auth::requireAuth();

        // Cek apakah loan ada dan milik user (atau admin)
        $query = "SELECT user_id FROM loans WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Peminjaman tidak ditemukan', 404);
        }

        $loan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Only allow deletion by owner or admin
        if ($loan['user_id'] != $user['userId'] && $user['role'] !== 'admin') {
            Response::error('Anda tidak memiliki akses untuk menghapus peminjaman ini', 403);
        }

        // Delete loan facilities first (foreign key)
        $query = "DELETE FROM loan_facilities WHERE loan_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Delete loan
        $query = "DELETE FROM loans WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'Peminjaman berhasil dihapus'
            ], 200);
        } else {
            Response::error('Gagal menghapus peminjaman', 500);
        }
    }

    /**
     * Helper: Get facilities for a loan
     */
    private function getLoanFacilities($loanId) {
        $query = "SELECT f.* FROM facilities f 
                  INNER JOIN loan_facilities lf ON f.id = lf.facility_id 
                  WHERE lf.loan_id = :loan_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':loan_id', $loanId);
        $stmt->execute();
        
        $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Parse features JSON
        foreach ($facilities as &$facility) {
            if (isset($facility['features'])) {
                $facility['features'] = json_decode($facility['features'], true);
            }
        }
        
        return $facilities;
    }
}
