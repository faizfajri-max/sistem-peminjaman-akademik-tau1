<?php
/**
 * Facilities Routes
 * Endpoint untuk CRUD fasilitas
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/response.php';

class FacilitiesRoutes {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * GET /api/facilities
     * List semua fasilitas dengan filter opsional
     */
    public function list() {
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;

        $query = "SELECT * FROM facilities WHERE 1=1";
        
        if ($type) {
            $query .= " AND type = :type";
        }
        
        if ($search) {
            $query .= " AND (name LIKE :search OR location LIKE :search)";
        }
        
        $query .= " ORDER BY name ASC";

        $stmt = $this->conn->prepare($query);
        
        if ($type) {
            $stmt->bindParam(':type', $type);
        }
        
        if ($search) {
            $searchParam = "%$search%";
            $stmt->bindParam(':search', $searchParam);
        }
        
        $stmt->execute();
        $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parse features JSON
        foreach ($facilities as &$facility) {
            if (isset($facility['features'])) {
                $facility['features'] = json_decode($facility['features'], true);
            }
        }

        Response::json([
            'success' => true,
            'data' => $facilities
        ], 200);
    }

    /**
     * GET /api/facilities/:id
     * Detail fasilitas
     */
    public function detail($id) {
        $query = "SELECT * FROM facilities WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Fasilitas tidak ditemukan', 404);
        }

        $facility = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (isset($facility['features'])) {
            $facility['features'] = json_decode($facility['features'], true);
        }

        Response::json([
            'success' => true,
            'data' => $facility
        ], 200);
    }

    /**
     * POST /api/facilities
     * Tambah fasilitas baru (admin only)
     */
    public function create() {
        $user = Auth::requireRole(['admin','staff']);
        
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        $required = ['name', 'type', 'capacity', 'location'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                Response::error("Field $field wajib diisi", 400);
            }
        }

        $name = trim($data['name']);
        $type = trim($data['type']);
        $capacity = (int)$data['capacity'];
        $location = trim($data['location']);
    $description = isset($data['description']) ? trim($data['description']) : null;
    $features = isset($data['features']) ? json_encode($data['features']) : json_encode([]);

        // Insert fasilitas
        // Support optional description column if exists
        $hasDescription = false;
        try {
            $colStmt = $this->conn->query("SHOW COLUMNS FROM facilities LIKE 'description'");
            $hasDescription = $colStmt && $colStmt->rowCount() > 0;
        } catch (Exception $e) { /* ignore */ }

        if ($hasDescription) {
            $query = "INSERT INTO facilities (name, type, capacity, location, description, features, created_at) 
                      VALUES (:name, :type, :capacity, :location, :description, :features, NOW())";
        } else {
            $query = "INSERT INTO facilities (name, type, capacity, location, features, created_at) 
                      VALUES (:name, :type, :capacity, :location, :features, NOW())";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':capacity', $capacity);
        $stmt->bindParam(':location', $location);
        if ($hasDescription) { $stmt->bindParam(':description', $description); }
        $stmt->bindParam(':features', $features);

        if ($stmt->execute()) {
            $facilityId = $this->conn->lastInsertId();
            
            Response::json([
                'success' => true,
                'message' => 'Fasilitas berhasil ditambahkan',
                'data' => [
                    'id' => $facilityId,
                    'name' => $name,
                    'type' => $type,
                    'capacity' => $capacity,
                    'location' => $location,
                    'description' => $description,
                    'features' => json_decode($features, true)
                ]
            ], 201);
        } else {
            Response::error('Gagal menambahkan fasilitas', 500);
        }
    }

    /**
     * PUT /api/facilities/:id
     * Update fasilitas (admin only)
     */
    public function update($id) {
        $user = Auth::requireRole(['admin','staff']);
        
        $data = json_decode(file_get_contents("php://input"), true);

        // Cek apakah fasilitas ada
        $query = "SELECT id FROM facilities WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Fasilitas tidak ditemukan', 404);
        }

        // Build update query
        $updates = [];
        $params = [':id' => $id];

        if (isset($data['name'])) {
            $updates[] = "name = :name";
            $params[':name'] = trim($data['name']);
        }
        if (isset($data['type'])) {
            $updates[] = "type = :type";
            $params[':type'] = trim($data['type']);
        }
        if (isset($data['capacity'])) {
            $updates[] = "capacity = :capacity";
            $params[':capacity'] = (int)$data['capacity'];
        }
        if (isset($data['location'])) {
            $updates[] = "location = :location";
            $params[':location'] = trim($data['location']);
        }
        if (isset($data['features'])) {
            $updates[] = "features = :features";
            $params[':features'] = json_encode($data['features']);
        }
        // Optional description if column exists
        try {
            $colStmt = $this->conn->query("SHOW COLUMNS FROM facilities LIKE 'description'");
            $hasDescription = $colStmt && $colStmt->rowCount() > 0;
            if ($hasDescription && isset($data['description'])) {
                $updates[] = "description = :description";
                $params[':description'] = trim($data['description']);
            }
        } catch (Exception $e) { /* ignore */ }

        if (empty($updates)) {
            Response::error('Tidak ada data yang diupdate', 400);
        }

        $query = "UPDATE facilities SET " . implode(', ', $updates) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if ($stmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'Fasilitas berhasil diupdate'
            ], 200);
        } else {
            Response::error('Gagal mengupdate fasilitas', 500);
        }
    }

    /**
     * DELETE /api/facilities/:id
     * Hapus fasilitas (admin only)
     */
    public function delete($id) {
        $user = Auth::requireRole(['admin']);

        // Cek apakah fasilitas ada
        $query = "SELECT id FROM facilities WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Fasilitas tidak ditemukan', 404);
        }

        // Hapus fasilitas
        $query = "DELETE FROM facilities WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'Fasilitas berhasil dihapus'
            ], 200);
        } else {
            Response::error('Gagal menghapus fasilitas', 500);
        }
    }
}
