<?php
/**
 * Bookings Routes
 * Endpoint untuk peminjaman dari form publik (borrow.html)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/response.php';

class BookingsRoutes {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * POST /api/bookings
     * Submit peminjaman baru dari form publik (tanpa auth)
     */
    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input - hanya yang benar-benar wajib
        $required = ['bookingId', 'borrowerName', 'facilityName', 'useDate', 'startTime', 'endTime'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                Response::error("Field $field wajib diisi", 400);
            }
        }

        $bookingId = trim($data['bookingId']);
        $borrowerName = trim($data['borrowerName']);
        $identity = isset($data['identity']) ? trim($data['identity']) : '';
        $unit = isset($data['unit']) ? trim($data['unit']) : '';
        $facilityId = isset($data['facilityId']) ? (int)$data['facilityId'] : null;
        $facilityName = trim($data['facilityName']);
        $roomType = isset($data['roomType']) ? trim($data['roomType']) : '';
        $useDate = $data['useDate'];
        $startTime = $data['startTime'];
        $endTime = $data['endTime'];
        $notes = isset($data['notes']) ? trim($data['notes']) : '';
        $documentName = isset($data['documentName']) ? trim($data['documentName']) : null;
        $status = 'pending';

        // Combine date + time
        $startDate = $useDate;
        $endDate = $useDate;

        // Insert booking record
        $query = "INSERT INTO bookings 
                  (booking_id, borrower_name, identity, unit, facility_id, facility_name, room_type, 
                   start_date, end_date, start_time, end_time, notes, document_name, status, created_at) 
                  VALUES 
                  (:booking_id, :borrower_name, :identity, :unit, :facility_id, :facility_name, :room_type,
                   :start_date, :end_date, :start_time, :end_time, :notes, :document_name, :status, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $bookingId);
        $stmt->bindParam(':borrower_name', $borrowerName);
        $stmt->bindParam(':identity', $identity);
        $stmt->bindParam(':unit', $unit);
        $stmt->bindParam(':facility_id', $facilityId);
        $stmt->bindParam(':facility_name', $facilityName);
        $stmt->bindParam(':room_type', $roomType);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':start_time', $startTime);
        $stmt->bindParam(':end_time', $endTime);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':document_name', $documentName);
        $stmt->bindParam(':status', $status);

        if (!$stmt->execute()) {
            Response::error('Gagal menyimpan peminjaman', 500);
        }

        $id = $this->conn->lastInsertId();

        // Insert booking facilities jika ada
        if (isset($data['facilities']) && is_array($data['facilities'])) {
            foreach ($data['facilities'] as $facility) {
                if (isset($facility['item']) && isset($facility['quantity'])) {
                    $query = "INSERT INTO booking_facilities 
                              (booking_id, facility_name, item, quantity) 
                              VALUES (:booking_id, :facility_name, :item, :quantity)";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':booking_id', $bookingId);
                    $stmt->bindParam(':facility_name', $facilityName);
                    $stmt->bindParam(':item', $facility['item']);
                    $stmt->bindParam(':quantity', $facility['quantity']);
                    $stmt->execute();
                }
            }
        }

        Response::json([
            'success' => true,
            'message' => 'Peminjaman berhasil diajukan',
            'data' => [
                'id' => $id,
                'bookingId' => $bookingId,
                'status' => $status
            ]
        ], 201);
    }

    /**
     * GET /api/bookings/:bookingId
     * Get detail booking by booking_id (BK_xxx format)
     */
    public function detail($bookingId) {
        $query = "SELECT * FROM bookings WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $bookingId);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Booking tidak ditemukan', 404);
        }

        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get facilities for this booking
        $query = "SELECT * FROM booking_facilities WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $booking['id']);
        $stmt->execute();
        $booking['facilities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::json([
            'success' => true,
            'loan' => $booking  // Use 'loan' key for compatibility with frontend
        ], 200);
    }

    /**
     * GET /api/bookings
     * List all bookings with filters
     */
    public function list() {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $from = isset($_GET['from']) ? $_GET['from'] : null;
        $to = isset($_GET['to']) ? $_GET['to'] : null;

        $query = "SELECT * FROM bookings WHERE 1=1";
        $params = [];
        
        if ($status) {
            $query .= " AND status = :status";
            $params[':status'] = $status;
        }
        
        if ($from) {
            $query .= " AND start_date >= :from";
            $params[':from'] = $from;
        }
        
        if ($to) {
            $query .= " AND end_date <= :to";
            $params[':to'] = $to;
        }
        
        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get facilities for each booking
        foreach ($bookings as &$booking) {
            $query = "SELECT * FROM booking_facilities WHERE booking_id = :booking_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $booking['booking_id']);
            $stmt->execute();
            $booking['facilities'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        Response::json([
            'success' => true,
            'data' => $bookings
        ], 200);
    }

    /**
     * PATCH /api/bookings/:bookingId/status
     * Update status booking (admin/staff)
     */
    public function updateStatus($bookingId) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['status'])) {
            Response::error('Status wajib diisi', 400);
        }

        $status = $data['status'];
        $validStatuses = ['pending', 'approved', 'rejected', 'done'];
        
        if (!in_array($status, $validStatuses)) {
            Response::error('Status tidak valid. Pilihan: ' . implode(', ', $validStatuses), 400);
        }

        $query = "UPDATE bookings SET status = :status, updated_at = NOW() 
                  WHERE booking_id = :booking_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':booking_id', $bookingId);

        if ($stmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'Status booking berhasil diupdate'
            ], 200);
        } else {
            Response::error('Gagal mengupdate status', 500);
        }
    }
}
