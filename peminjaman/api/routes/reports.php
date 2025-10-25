<?php
/**
 * Reports Routes
 * Endpoint untuk laporan dan statistik
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/response.php';

class ReportsRoutes {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * GET /api/reports/summary
     * Ringkasan statistik peminjaman (admin/staff only)
     */
    public function summary() {
        $user = Auth::requireRole(['admin', 'staff']);

        // Total loans by status
        $query = "SELECT status, COUNT(*) as count FROM loans GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Total loans
        $query = "SELECT COUNT(*) as total FROM loans";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $totalLoans = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total users
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total facilities
        $query = "SELECT COUNT(*) as total FROM facilities";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $totalFacilities = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Recent loans (last 10)
        $query = "SELECT l.*, u.name as user_name, u.email as user_email 
                  FROM loans l 
                  LEFT JOIN users u ON l.user_id = u.id 
                  ORDER BY l.created_at DESC 
                  LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $recentLoans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Most borrowed facilities
        $query = "SELECT f.id, f.name, f.type, COUNT(lf.facility_id) as borrow_count 
                  FROM facilities f 
                  LEFT JOIN loan_facilities lf ON f.id = lf.facility_id 
                  GROUP BY f.id 
                  ORDER BY borrow_count DESC 
                  LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $popularFacilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loans by month (current year)
        $currentYear = date('Y');
        $query = "SELECT MONTH(created_at) as month, COUNT(*) as count 
                  FROM loans 
                  WHERE YEAR(created_at) = :year 
                  GROUP BY MONTH(created_at) 
                  ORDER BY month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $currentYear);
        $stmt->execute();
        $loansByMonth = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loans by facility type
        $query = "SELECT l.room_type, COUNT(*) as count 
                  FROM loans l 
                  WHERE l.room_type IS NOT NULL 
                  GROUP BY l.room_type 
                  ORDER BY count DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $loansByType = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::json([
            'success' => true,
            'data' => [
                'total_loans' => (int)$totalLoans,
                'total_users' => (int)$totalUsers,
                'total_facilities' => (int)$totalFacilities,
                'status_counts' => [
                    'pending' => isset($statusCounts['pending']) ? (int)$statusCounts['pending'] : 0,
                    'approved' => isset($statusCounts['approved']) ? (int)$statusCounts['approved'] : 0,
                    'rejected' => isset($statusCounts['rejected']) ? (int)$statusCounts['rejected'] : 0,
                    'done' => isset($statusCounts['done']) ? (int)$statusCounts['done'] : 0,
                ],
                'recent_loans' => $recentLoans,
                'popular_facilities' => $popularFacilities,
                'loans_by_month' => $loansByMonth,
                'loans_by_type' => $loansByType,
            ]
        ], 200);
    }

    /**
     * GET /api/reports/user-stats
     * Statistik peminjaman per user
     */
    public function userStats() {
        $user = Auth::requireAuth();
        $userId = $user['userId'];

        // Allow admin to view any user's stats
        if ($user['role'] === 'admin' && isset($_GET['userId'])) {
            $userId = $_GET['userId'];
        }

        // Total loans by user
        $query = "SELECT COUNT(*) as total FROM loans WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $totalLoans = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Loans by status for user
        $query = "SELECT status, COUNT(*) as count FROM loans WHERE user_id = :user_id GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Recent loans by user
        $query = "SELECT l.* FROM loans l WHERE l.user_id = :user_id ORDER BY l.created_at DESC LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $recentLoans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::json([
            'success' => true,
            'data' => [
                'total_loans' => (int)$totalLoans,
                'status_counts' => [
                    'pending' => isset($statusCounts['pending']) ? (int)$statusCounts['pending'] : 0,
                    'approved' => isset($statusCounts['approved']) ? (int)$statusCounts['approved'] : 0,
                    'rejected' => isset($statusCounts['rejected']) ? (int)$statusCounts['rejected'] : 0,
                    'done' => isset($statusCounts['done']) ? (int)$statusCounts['done'] : 0,
                ],
                'recent_loans' => $recentLoans,
            ]
        ], 200);
    }

    /**
     * GET /api/reports/facility-usage
     * Laporan penggunaan fasilitas (admin/staff only)
     */
    public function facilityUsage() {
        $user = Auth::requireRole(['admin', 'staff']);

        $facilityId = isset($_GET['facilityId']) ? $_GET['facilityId'] : null;

        if ($facilityId) {
            // Usage for specific facility
            $query = "SELECT l.*, u.name as user_name 
                      FROM loans l 
                      LEFT JOIN users u ON l.user_id = u.id 
                      INNER JOIN loan_facilities lf ON l.id = lf.loan_id 
                      WHERE lf.facility_id = :facility_id 
                      ORDER BY l.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':facility_id', $facilityId);
            $stmt->execute();
            $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Response::json([
                'success' => true,
                'data' => $loans
            ], 200);
        } else {
            // All facilities usage
            $query = "SELECT f.id, f.name, f.type, COUNT(lf.facility_id) as usage_count 
                      FROM facilities f 
                      LEFT JOIN loan_facilities lf ON f.id = lf.facility_id 
                      GROUP BY f.id 
                      ORDER BY usage_count DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Response::json([
                'success' => true,
                'data' => $facilities
            ], 200);
        }
    }
}
