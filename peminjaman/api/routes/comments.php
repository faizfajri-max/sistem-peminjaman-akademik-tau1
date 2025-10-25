<?php
/**
 * Comments Routes
 * Endpoint untuk komentar dan dokumentasi pengembalian
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/response.php';

class CommentsRoutes {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    /**
     * GET /api/comments/:loanId
     * List komentar untuk peminjaman tertentu
     */
    public function list($loanId) {
        $query = "SELECT c.*, u.name as user_name, u.email as user_email 
                  FROM comments c 
                  LEFT JOIN users u ON c.user_id = u.id 
                  WHERE c.loan_id = :loan_id 
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':loan_id', $loanId);
        $stmt->execute();
        
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Parse photos JSON
        foreach ($comments as &$comment) {
            if (isset($comment['photos'])) {
                $comment['photos'] = json_decode($comment['photos'], true);
            }
        }

        Response::json([
            'success' => true,
            'data' => $comments
        ], 200);
    }

    /**
     * POST /api/comments/:loanId
     * Tambah komentar + upload foto
     */
    public function create($loanId) {
        $user = Auth::requireAuth();

        // Cek apakah loan ada
        $query = "SELECT id FROM loans WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $loanId);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Peminjaman tidak ditemukan', 404);
        }

        $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
        
        if (empty($comment)) {
            Response::error('Komentar tidak boleh kosong', 400);
        }

        // Handle file uploads
        $photos = [];
        if (isset($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
            $photos = $this->handleFileUploads($_FILES['photos']);
        }

        $photosJson = json_encode($photos);

        // Insert comment
        $query = "INSERT INTO comments (loan_id, user_id, comment, photos, created_at) 
                  VALUES (:loan_id, :user_id, :comment, :photos, NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':loan_id', $loanId);
        $stmt->bindParam(':user_id', $user['userId']);
        $stmt->bindParam(':comment', $comment);
        $stmt->bindParam(':photos', $photosJson);

        if ($stmt->execute()) {
            $commentId = $this->conn->lastInsertId();
            
            Response::json([
                'success' => true,
                'message' => 'Komentar berhasil ditambahkan',
                'data' => [
                    'id' => $commentId,
                    'comment' => $comment,
                    'photos' => $photos
                ]
            ], 201);
        } else {
            Response::error('Gagal menambahkan komentar', 500);
        }
    }

    /**
     * PATCH /api/comments/:loanId/mark-returned
     * Tandai peminjaman selesai dikembalikan (admin/staff only)
     */
    public function markReturned($loanId) {
        $user = Auth::requireRole(['admin', 'staff']);

        // Cek apakah loan ada
        $query = "SELECT id FROM loans WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $loanId);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Peminjaman tidak ditemukan', 404);
        }

        // Update status to done
        $query = "UPDATE loans SET status = 'done', updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $loanId);

        if ($stmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'Peminjaman telah ditandai sebagai selesai'
            ], 200);
        } else {
            Response::error('Gagal mengupdate status', 500);
        }
    }

    /**
     * DELETE /api/comments/:commentId
     * Hapus komentar (admin only atau user yang membuat)
     */
    public function delete($commentId) {
        $user = Auth::requireAuth();

        // Cek apakah comment ada
        $query = "SELECT user_id, photos FROM comments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $commentId);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            Response::error('Komentar tidak ditemukan', 404);
        }

        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        // Only allow deletion by owner or admin
        if ($comment['user_id'] != $user['userId'] && $user['role'] !== 'admin') {
            Response::error('Anda tidak memiliki akses untuk menghapus komentar ini', 403);
        }

        // Delete photos from disk
        if (!empty($comment['photos'])) {
            $photos = json_decode($comment['photos'], true);
            foreach ($photos as $photo) {
                $filePath = UPLOAD_DIR . basename($photo);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        // Delete comment
        $query = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $commentId);

        if ($stmt->execute()) {
            Response::json([
                'success' => true,
                'message' => 'Komentar berhasil dihapus'
            ], 200);
        } else {
            Response::error('Gagal menghapus komentar', 500);
        }
    }

    /**
     * Helper: Handle multiple file uploads
     */
    private function handleFileUploads($files) {
        $uploadedFiles = [];
        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $fileName = $files['name'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            $fileSize = $files['size'][$i];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validate file extension
            if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
                continue;
            }

            // Validate file size
            if ($fileSize > MAX_FILE_SIZE) {
                continue;
            }

            // Generate unique filename
            $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
            $destination = UPLOAD_DIR . $newFileName;

            // Move uploaded file
            if (move_uploaded_file($fileTmpName, $destination)) {
                $uploadedFiles[] = '/api/uploads/' . $newFileName;
            }
        }

        return $uploadedFiles;
    }
}
