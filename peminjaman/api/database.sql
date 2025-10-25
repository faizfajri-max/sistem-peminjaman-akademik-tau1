-- Database Schema untuk Sistem Peminjaman Fasilitas Kampus
-- MySQL Database

-- Create database
CREATE DATABASE IF NOT EXISTS peminjaman_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE peminjaman_db;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: facilities
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    capacity INT NOT NULL DEFAULT 0,
    location VARCHAR(255) NOT NULL,
    features JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: loans (peminjaman)
CREATE TABLE IF NOT EXISTS loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    unit VARCHAR(255) NOT NULL,
    purpose TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    room_type VARCHAR(100),
    participants INT DEFAULT 0,
    notes TEXT,
    status ENUM('pending', 'approved', 'rejected', 'done') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: loan_facilities (relasi many-to-many antara loans dan facilities)
CREATE TABLE IF NOT EXISTS loan_facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id INT NOT NULL,
    facility_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
    INDEX idx_loan_id (loan_id),
    INDEX idx_facility_id (facility_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: comments (dokumentasi pengembalian)
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    loan_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    photos JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_loan_id (loan_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- SEED DATA (Data Awal)
-- =========================================

-- Insert default users dengan password yang sudah di-hash
-- Password untuk semua user: admin123
-- Generated: 2025-10-24
INSERT INTO users (name, email, password, role) VALUES
('Administrator', 'admin@kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'admin'),
('Staff Akademik', 'staff@kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'staff'),
('User Demo', 'user@kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'user'),
('Budi Santoso', 'budi@student.kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'user'),
('Ani Wijaya', 'ani@student.kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'user'),
('Dedi Prasetyo', 'dedi@student.kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'user');

-- Note: Semua password default adalah "admin123" untuk testing
-- Hash: $2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS
-- Untuk production, ganti dengan password yang lebih aman!

-- Insert facilities (Ruangan Kelas)
INSERT INTO facilities (name, type, capacity, location, features) VALUES
('Kelas 203', 'Kelas', 40, 'Gedung Utama Lantai 2', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 204', 'Kelas', 40, 'Gedung Utama Lantai 2', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 205', 'Kelas', 40, 'Gedung Utama Lantai 2', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 206', 'Kelas', 40, 'Gedung Utama Lantai 2', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 304', 'Kelas', 40, 'Gedung Utama Lantai 3', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 306', 'Kelas', 40, 'Gedung Utama Lantai 3', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 308', 'Kelas', 40, 'Gedung Utama Lantai 3', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 401', 'Kelas', 40, 'Gedung Utama Lantai 4', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 402', 'Kelas', 40, 'Gedung Utama Lantai 4', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 403', 'Kelas', 40, 'Gedung Utama Lantai 4', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 404', 'Kelas', 40, 'Gedung Utama Lantai 4', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 405', 'Kelas', 40, 'Gedung Utama Lantai 4', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 406', 'Kelas', 40, 'Gedung Utama Lantai 4', '["Proyektor", "AC", "Whiteboard"]'),
('Kelas 408', 'Kelas', 40, 'Gedung Utama Lantai 4', '["Proyektor", "AC", "Whiteboard"]'),

-- Fasilitas Lainnya
('Ballroom Kampus', 'Ballroom', 400, 'Gedung Serbaguna', '["Panggung", "Sound System", "LED"]'),
('Rans Room Studio', 'Rans Room', 20, 'Gedung Media', '["Podcast Mic", "Green Screen"]'),
('Ruang BUMR', 'BUMR', 25, 'Gedung Administrasi', '["AC", "WiFi"]'),
('Ruang LPPM Rapat', 'LPPM', 18, 'Gedung Riset', '["TV 55\\"", "VC Camera"]'),
('Ruang Diskusi Perpustakaan', 'Perpustakaan', 12, 'Perpustakaan', '["AC", "Whiteboard"]'),
('Studio Podcast', 'Ruang Podcast', 6, 'Gedung Media', '["Mic", "Mixer", "Akustik"]'),

-- Peralatan
('Kamera DSLR A', 'Peralatan', 1, 'Unit Multimedia', '["Body + Lensa 24-70"]'),
('Proyektor Portable', 'Peralatan', 1, 'Gudang Peralatan', '["HDMI", "Remote"]');

-- Insert contoh peminjaman (lebih lengkap)
INSERT INTO loans (user_id, unit, purpose, start_date, end_date, start_time, end_time, room_type, participants, notes, status) VALUES
-- Peminjaman Approved
(1, 'Fakultas Teknik', 'Seminar Workshop AI', '2025-10-25', '2025-10-25', '09:00:00', '16:00:00', 'Kelas', 50, 'Memerlukan proyektor dan sound system', 'approved'),
(4, 'Himpunan Mahasiswa Informatika', 'Workshop Web Development', '2025-10-26', '2025-10-26', '13:00:00', '17:00:00', 'Kelas', 35, 'Butuh proyektor dan koneksi internet', 'approved'),
(5, 'Unit Kegiatan Fotografi', 'Podcast Recording - Episode 5', '2025-10-27', '2025-10-27', '10:00:00', '12:00:00', 'Ruang Podcast', 4, 'Interview dengan narasumber alumni', 'approved'),
(6, 'Fakultas Ekonomi', 'Kuliah Tamu Entrepreneur', '2025-10-28', '2025-10-28', '08:00:00', '10:00:00', 'Ballroom', 150, 'Undangan pembicara dari luar', 'approved'),

-- Peminjaman Pending (menunggu approval)
(4, 'OSIS Kampus', 'Rapat Koordinasi Kegiatan', '2025-10-29', '2025-10-29', '14:00:00', '17:00:00', 'Kelas', 20, '', 'pending'),
(5, 'Unit Kesenian', 'Latihan Drama', '2025-10-30', '2025-10-30', '15:00:00', '18:00:00', 'Ballroom', 30, 'Persiapan pentas seni bulan depan', 'pending'),
(6, 'Fakultas Hukum', 'Diskusi Panel Hukum Digital', '2025-10-31', '2025-10-31', '09:00:00', '12:00:00', 'LPPM', 15, 'Diskusi dengan dosen', 'pending'),

-- Peminjaman Done (sudah selesai)
(4, 'BEM Fakultas', 'Rapat Evaluasi Program', '2025-10-20', '2025-10-20', '13:00:00', '16:00:00', 'Kelas', 25, 'Evaluasi program kerja semester ini', 'done'),
(5, 'Komunitas Sains', 'Eksperimen Fisika', '2025-10-21', '2025-10-21', '10:00:00', '14:00:00', 'Kelas', 15, 'Butuh proyektor untuk presentasi', 'done'),
(6, 'Unit Jurnalistik', 'Produksi Video Campaign', '2025-10-22', '2025-10-22', '08:00:00', '11:00:00', 'Rans Room', 8, 'Shooting video kampus', 'done'),

-- Peminjaman Rejected
(4, 'Klub Olahraga', 'Briefing Tim', '2025-11-01', '2025-11-01', '18:00:00', '20:00:00', 'Kelas', 40, 'Briefing setelah jam kuliah', 'rejected'),
(5, 'Himpunan Matematika', 'Belajar Bersama', '2025-11-02', '2025-11-02', '19:00:00', '21:00:00', 'Kelas', 20, 'Belajar malam', 'rejected');

-- Insert relasi loan_facilities (fasilitas yang dipinjam)
INSERT INTO loan_facilities (loan_id, facility_id) VALUES
-- Loan 1: Seminar Workshop AI
(1, 1),  -- Kelas 203
(1, 21), -- Kamera DSLR A
(1, 22), -- Proyektor Portable

-- Loan 2: Workshop Web Development
(2, 5),  -- Kelas 304
(2, 22), -- Proyektor Portable

-- Loan 3: Podcast Recording
(3, 20), -- Studio Podcast

-- Loan 4: Kuliah Tamu
(4, 15), -- Ballroom Kampus

-- Loan 5: Rapat Koordinasi (pending)
(5, 7),  -- Kelas 308

-- Loan 6: Latihan Drama (pending)
(6, 15), -- Ballroom Kampus

-- Loan 7: Diskusi Panel (pending)
(7, 18), -- Ruang LPPM Rapat

-- Loan 8: Rapat Evaluasi (done)
(8, 2),  -- Kelas 204

-- Loan 9: Eksperimen Fisika (done)
(9, 6),  -- Kelas 306
(9, 22), -- Proyektor Portable

-- Loan 10: Produksi Video (done)
(10, 16), -- Rans Room Studio
(10, 21); -- Kamera DSLR A

-- Insert contoh comments (dokumentasi pengembalian)
INSERT INTO comments (loan_id, user_id, comment, photos) VALUES
(1, 1, 'Fasilitas dalam kondisi baik, acara berjalan lancar. Terima kasih!', '[]'),
(3, 5, 'Recording berhasil, audio jernih. Peralatan podcast sangat memadai.', '[]'),
(8, 4, 'Ruangan bersih dan nyaman. AC berfungsi dengan baik.', '[]'),
(9, 5, 'Proyektor berfungsi sempurna. Eksperimen berjalan lancar.', '[]'),
(10, 6, 'Kamera dan studio dalam kondisi sangat baik. Hasil video memuaskan.', '[]');
