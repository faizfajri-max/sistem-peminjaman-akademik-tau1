-- =========================================
-- QUICK UPDATE - Password Only (Safe Version)
-- =========================================
-- File ini hanya update password, tidak insert data baru
-- Aman untuk dijalankan berkali-kali

USE peminjaman_db;

-- Update password untuk 3 default users
-- Password: admin123
-- Hash: $2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS

UPDATE users 
SET password = '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS' 
WHERE email IN ('admin@kampus.ac.id', 'staff@kampus.ac.id', 'user@kampus.ac.id');

-- Verifikasi
SELECT 
    email, 
    role,
    CASE 
        WHEN password = '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS' 
        THEN '✅ Updated' 
        ELSE '❌ Not Updated' 
    END as status
FROM users 
WHERE email IN ('admin@kampus.ac.id', 'staff@kampus.ac.id', 'user@kampus.ac.id');

SELECT '✅ Password berhasil diupdate!' as result;
SELECT 'Sekarang bisa login dengan password: admin123' as info;
