<?php
/**
 * Authentication Routes
 * Endpoint untuk register, login, dan me
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/response.php';

class AuthRoutes {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * POST /api/auth/register
     * Register pengguna baru
     */
    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            Response::error('Nama, email, dan password wajib diisi', 400);
        }

        $name = trim($data['name']);
        $email = trim($data['email']);
        $password = $data['password'];

        if (empty($name) || empty($email) || empty($password)) {
            Response::error('Nama, email, dan password tidak boleh kosong', 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Format email tidak valid', 400);
        }

        // Validasi domain email - hanya student, admin, atau staff dari tau.ac.id
        $allowedDomains = ['@student.tau.ac.id', '@admin.tau.ac.id', '@staff.tau.ac.id'];
        $emailDomainValid = false;
        foreach ($allowedDomains as $domain) {
            if (substr($email, -strlen($domain)) === $domain) {
                $emailDomainValid = true;
                break;
            }
        }
        
        if (!$emailDomainValid) {
            Response::error('Hanya email @student.tau.ac.id, @admin.tau.ac.id, atau @staff.tau.ac.id yang diperbolehkan', 400);
        }

        // Cek apakah email sudah terdaftar
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            Response::error('Email sudah terdaftar', 400);
        }

        // Hash password
        $hashedPassword = Auth::hashPassword($password);
        $role = isset($data['role']) ? $data['role'] : 'user'; // default role: user

        // Insert user baru
        $query = "INSERT INTO users (name, email, password, role, created_at) 
                  VALUES (:name, :email, :password, :role, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $userId = $this->conn->lastInsertId();
            $token = Auth::generateToken($userId, $email, $role, $name);

            Response::json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'user' => [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ],
                'token' => $token
            ], 201);
        } else {
            Response::error('Registrasi gagal', 500);
        }
    }

    /**
     * POST /api/auth/login
     * Login pengguna
     */
    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (!isset($data['email']) || !isset($data['password'])) {
            Response::error('Email dan password wajib diisi', 400);
        }

        $email = trim($data['email']);
        $password = $data['password'];

        // Validasi domain email - hanya student, admin, atau staff dari tau.ac.id
        $allowedDomains = ['@student.tau.ac.id', '@admin.tau.ac.id', '@staff.tau.ac.id'];
        $emailDomainValid = false;
        foreach ($allowedDomains as $domain) {
            if (substr($email, -strlen($domain)) === $domain) {
                $emailDomainValid = true;
                break;
            }
        }
        
        if (!$emailDomainValid) {
            Response::error('Hanya email @student.tau.ac.id, @admin.tau.ac.id, atau @staff.tau.ac.id yang diperbolehkan', 400);
        }

        // Cari user berdasarkan email
        $query = "SELECT id, name, email, password, role FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Email atau password salah', 401);
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password
        if (!Auth::verifyPassword($password, $user['password'])) {
            Response::error('Email atau password salah', 401);
        }

        // Generate token
        $token = Auth::generateToken($user['id'], $user['email'], $user['role'], $user['name']);

        Response::json([
            'success' => true,
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ],
            'token' => $token
        ], 200);
    }

    /**
     * GET /api/auth/me
     * Get current user info
     */
    public function me() {
        $user = Auth::requireAuth();

        // Get full user data from database
        $query = "SELECT id, name, email, role, created_at FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $user['userId']);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('User tidak ditemukan', 404);
        }

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        Response::json([
            'success' => true,
            'user' => $userData
        ], 200);
    }

    /**
     * GET /api/users
     * Get all users (admin/staff only)
     */
    public function getUsers() {
        $user = Auth::requireRole(['admin', 'staff']);

        // Get query parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $role = isset($_GET['role']) ? trim($_GET['role']) : '';

        $offset = ($page - 1) * $limit;

        // Build query
        $whereConditions = [];
        $params = [];

        if (!empty($search)) {
            $whereConditions[] = "(name LIKE :search OR email LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($role) && in_array($role, ['admin', 'staff', 'user'])) {
            $whereConditions[] = "role = :role";
            $params[':role'] = $role;
        }

        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM users $whereClause";
        $countStmt = $this->conn->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get users
        $query = "SELECT id, name, email, role, created_at, updated_at 
                  FROM users 
                  $whereClause
                  ORDER BY created_at DESC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        Response::json([
            'success' => true,
            'users' => $users,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$total,
                'totalPages' => ceil($total / $limit)
            ]
        ], 200);
    }

    /**
     * PUT /api/users/:id/role
     * Update user role (admin only)
     */
    public function updateUserRole($userId) {
        $currentUser = Auth::requireRole(['admin']);

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['role'])) {
            Response::error('Role wajib diisi', 400);
        }

        $newRole = trim($data['role']);

        if (!in_array($newRole, ['admin', 'staff', 'user'])) {
            Response::error('Role harus admin, staff, atau user', 400);
        }

        // Cek apakah user ada
        $checkQuery = "SELECT id, name, email, role FROM users WHERE id = :id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $userId);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            Response::error('User tidak ditemukan', 404);
        }

        $targetUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Prevent admin dari mengubah role diri sendiri
        if ($targetUser['id'] == $currentUser['userId']) {
            Response::error('Tidak dapat mengubah role sendiri', 403);
        }

        // Update role
        $updateQuery = "UPDATE users SET role = :role, updated_at = NOW() WHERE id = :id";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(':role', $newRole);
        $updateStmt->bindParam(':id', $userId);

        if ($updateStmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'Role user berhasil diubah',
                'user' => [
                    'id' => $targetUser['id'],
                    'name' => $targetUser['name'],
                    'email' => $targetUser['email'],
                    'role' => $newRole
                ]
            ], 200);
        } else {
            Response::error('Gagal mengubah role user', 500);
        }
    }

    /**
     * DELETE /api/users/:id
     * Delete user (admin only)
     */
    public function deleteUser($userId) {
        $currentUser = Auth::requireRole(['admin']);

        // Cek apakah user ada
        $checkQuery = "SELECT id, name, email FROM users WHERE id = :id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $userId);
        $checkStmt->execute();

        if ($checkStmt->rowCount() === 0) {
            Response::error('User tidak ditemukan', 404);
        }

        $targetUser = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Prevent admin dari menghapus diri sendiri
        if ($targetUser['id'] == $currentUser['userId']) {
            Response::error('Tidak dapat menghapus akun sendiri', 403);
        }

        // Delete user (cascade akan hapus loans dan comments terkait)
        $deleteQuery = "DELETE FROM users WHERE id = :id";
        $deleteStmt = $this->conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $userId);

        if ($deleteStmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'User berhasil dihapus',
                'user' => $targetUser
            ], 200);
        } else {
            Response::error('Gagal menghapus user', 500);
        }
    }
}
