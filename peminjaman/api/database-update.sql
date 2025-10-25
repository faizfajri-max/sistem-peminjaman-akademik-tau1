-- =========================================
-- UPDATE DATABASE - Fix Password & Add Sample Data
-- =========================================
-- Jalankan file ini setelah database.sql
-- Atau jalankan langsung jika database sudah ada

USE peminjaman_db;

-- =========================================
-- 1. UPDATE PASSWORD USERS
-- =========================================
-- Hash untuk password "admin123"
-- PENTING: Ini adalah hash yang valid untuk testing
-- Generated: 2025-10-24

UPDATE users SET password = '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS' 
WHERE email = 'admin@kampus.ac.id';

UPDATE users SET password = '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS' 
WHERE email = 'staff@kampus.ac.id';

UPDATE users SET password = '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS' 
WHERE email = 'user@kampus.ac.id';

-- =========================================
-- 2. TAMBAH USER SAMPLE (Optional)
-- =========================================

-- Cek apakah user sudah ada, insert jika belum
INSERT IGNORE INTO users (name, email, password, role, created_at) VALUES
('Budi Santoso', 'budi@student.kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'user', NOW()),
('Ani Wijaya', 'ani@student.kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'user', NOW()),
('Dedi Prasetyo', 'dedi@student.kampus.ac.id', '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', 'user', NOW());

-- =========================================
-- 3. TAMBAH DATA PEMINJAMAN LENGKAP
-- =========================================

-- Hapus data peminjaman lama (optional - hati-hati!)
-- TRUNCATE TABLE comments;
-- TRUNCATE TABLE loan_facilities;
-- TRUNCATE TABLE loans;

-- Get user IDs untuk insert loans
SET @admin_id = (SELECT id FROM users WHERE email = 'admin@kampus.ac.id' LIMIT 1);
SET @staff_id = (SELECT id FROM users WHERE email = 'staff@kampus.ac.id' LIMIT 1);
SET @user_id = (SELECT id FROM users WHERE email = 'user@kampus.ac.id' LIMIT 1);
SET @budi_id = (SELECT id FROM users WHERE email = 'budi@student.kampus.ac.id' LIMIT 1);
SET @ani_id = (SELECT id FROM users WHERE email = 'ani@student.kampus.ac.id' LIMIT 1);
SET @dedi_id = (SELECT id FROM users WHERE email = 'dedi@student.kampus.ac.id' LIMIT 1);

-- Insert peminjaman lengkap dengan berbagai status
-- PENTING: Gunakan variable @user_id agar tidak error foreign key
INSERT INTO loans (user_id, unit, purpose, start_date, end_date, start_time, end_time, room_type, participants, notes, status, created_at) VALUES
-- Approved (Disetujui)
(@admin_id, 'Fakultas Teknik', 'Seminar Workshop AI', '2025-10-25', '2025-10-25', '09:00:00', '16:00:00', 'Kelas', 50, 'Memerlukan proyektor dan sound system', 'approved', NOW()),
(@budi_id, 'Himpunan Mahasiswa Informatika', 'Workshop Web Development', '2025-10-26', '2025-10-26', '13:00:00', '17:00:00', 'Kelas', 35, 'Butuh proyektor dan koneksi internet', 'approved', NOW()),
(@ani_id, 'Unit Kegiatan Fotografi', 'Podcast Recording - Episode 5', '2025-10-27', '2025-10-27', '10:00:00', '12:00:00', 'Ruang Podcast', 4, 'Interview dengan narasumber alumni', 'approved', NOW()),
(@dedi_id, 'Fakultas Ekonomi', 'Kuliah Tamu Entrepreneur', '2025-10-28', '2025-10-28', '08:00:00', '10:00:00', 'Ballroom', 150, 'Undangan pembicara dari luar', 'approved', NOW()),

-- Pending (Menunggu approval)
(@budi_id, 'OSIS Kampus', 'Rapat Koordinasi Kegiatan', '2025-10-29', '2025-10-29', '14:00:00', '17:00:00', 'Kelas', 20, '', 'pending', NOW()),
(@ani_id, 'Unit Kesenian', 'Latihan Drama', '2025-10-30', '2025-10-30', '15:00:00', '18:00:00', 'Ballroom', 30, 'Persiapan pentas seni bulan depan', 'pending', NOW()),
(@dedi_id, 'Fakultas Hukum', 'Diskusi Panel Hukum Digital', '2025-10-31', '2025-10-31', '09:00:00', '12:00:00', 'LPPM', 15, 'Diskusi dengan dosen', 'pending', NOW()),

-- Done (Selesai)
(@budi_id, 'BEM Fakultas', 'Rapat Evaluasi Program', '2025-10-20', '2025-10-20', '13:00:00', '16:00:00', 'Kelas', 25, 'Evaluasi program kerja semester ini', 'done', '2025-10-19 10:00:00'),
(@ani_id, 'Komunitas Sains', 'Eksperimen Fisika', '2025-10-21', '2025-10-21', '10:00:00', '14:00:00', 'Kelas', 15, 'Butuh proyektor untuk presentasi', 'done', '2025-10-18 14:30:00'),
(@dedi_id, 'Unit Jurnalistik', 'Produksi Video Campaign', '2025-10-22', '2025-10-22', '08:00:00', '11:00:00', 'Rans Room', 8, 'Shooting video kampus', 'done', '2025-10-17 09:15:00'),

-- Rejected (Ditolak)
(@budi_id, 'Klub Olahraga', 'Briefing Tim', '2025-11-01', '2025-11-01', '18:00:00', '20:00:00', 'Kelas', 40, 'Briefing setelah jam kuliah', 'rejected', NOW()),
(@ani_id, 'Himpunan Matematika', 'Belajar Bersama', '2025-11-02', '2025-11-02', '19:00:00', '21:00:00', 'Kelas', 20, 'Belajar malam', 'rejected', NOW());

-- Insert relasi loan_facilities
-- Gunakan ID loans yang baru saja diinsert
SET @loan1 = (SELECT id FROM loans WHERE unit = 'Fakultas Teknik' AND purpose = 'Seminar Workshop AI' ORDER BY created_at DESC LIMIT 1);
SET @loan2 = (SELECT id FROM loans WHERE unit = 'Himpunan Mahasiswa Informatika' ORDER BY created_at DESC LIMIT 1);
SET @loan3 = (SELECT id FROM loans WHERE unit = 'Unit Kegiatan Fotografi' ORDER BY created_at DESC LIMIT 1);
SET @loan4 = (SELECT id FROM loans WHERE unit = 'Fakultas Ekonomi' ORDER BY created_at DESC LIMIT 1);
SET @loan8 = (SELECT id FROM loans WHERE unit = 'BEM Fakultas' ORDER BY created_at DESC LIMIT 1);
SET @loan9 = (SELECT id FROM loans WHERE unit = 'Komunitas Sains' ORDER BY created_at DESC LIMIT 1);
SET @loan10 = (SELECT id FROM loans WHERE unit = 'Unit Jurnalistik' ORDER BY created_at DESC LIMIT 1);

INSERT INTO loan_facilities (loan_id, facility_id) VALUES
(@loan1, 1),  -- Kelas 203
(@loan1, 21), -- Kamera DSLR A
(@loan1, 22), -- Proyektor Portable
(@loan2, 5),  -- Kelas 304
(@loan2, 22), -- Proyektor Portable
(@loan3, 20), -- Studio Podcast
(@loan4, 15), -- Ballroom Kampus
(@loan8, 2),  -- Kelas 204
(@loan9, 6),  -- Kelas 306
(@loan9, 22), -- Proyektor Portable
(@loan10, 16), -- Rans Room Studio
(@loan10, 21); -- Kamera DSLR A

-- Insert comments untuk peminjaman yang sudah selesai
INSERT INTO comments (loan_id, user_id, comment, photos, created_at) VALUES
(@loan1, @admin_id, 'Fasilitas dalam kondisi baik, acara berjalan lancar. Terima kasih!', '[]', NOW()),
(@loan3, @ani_id, 'Recording berhasil, audio jernih. Peralatan podcast sangat memadai.', '[]', NOW()),
(@loan8, @budi_id, 'Ruangan bersih dan nyaman. AC berfungsi dengan baik.', '[]', NOW()),
(@loan9, @ani_id, 'Proyektor berfungsi sempurna. Eksperimen berjalan lancar.', '[]', NOW()),
(@loan10, @dedi_id, 'Kamera dan studio dalam kondisi sangat baik. Hasil video memuaskan.', '[]', NOW());

-- =========================================
-- VERIFIKASI
-- =========================================

-- Cek jumlah users
SELECT 'Total Users:' as info, COUNT(*) as count FROM users;

-- Cek users dengan rolenya
SELECT id, name, email, role FROM users;

-- Cek jumlah peminjaman per status
SELECT 
  status,
  COUNT(*) as total
FROM loans
GROUP BY status
ORDER BY FIELD(status, 'pending', 'approved', 'done', 'rejected');

-- Cek peminjaman terbaru
SELECT 
  l.id,
  l.unit,
  l.purpose,
  l.status,
  u.name as borrower
FROM loans l
JOIN users u ON l.user_id = u.id
ORDER BY l.created_at DESC
LIMIT 10;

SELECT '✅ Database berhasil diupdate!' as result;
SELECT 'Login dengan:' as info;
SELECT '- Admin: admin@kampus.ac.id / admin123' as credentials;
SELECT '- Staff: staff@kampus.ac.id / admin123' as credentials2;
SELECT '- User: user@kampus.ac.id / admin123' as credentials3;
