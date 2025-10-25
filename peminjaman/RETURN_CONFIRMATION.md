# 📸 Fitur Konfirmasi & Dokumentasi Pengembalian Fasilitas

## Overview
Fitur ini memungkinkan peminjam dan staff untuk berkomunikasi tentang pengembalian fasilitas melalui komentar dan upload foto bukti pengembalian.

## Cara Menggunakan

### 1. Sebagai Peminjam
1. Buka halaman **Konfirmasi & Dokumentasi** (`confirmation.html`)
2. Pilih pengajuan peminjaman dari daftar "Riwayat Pengajuan Saya"
3. Klik **Lihat Detail** pada pengajuan yang sudah disetujui (status: `approved`)
4. Scroll ke bagian **💬 Timeline Pengembalian**
5. Tulis komentar, contoh: *"Barang sudah dikembalikan ke ruang 203"*
6. (Opsional) Klik **📷 Upload Foto Bukti** untuk upload foto pengembalian
7. Klik **Kirim Komentar**

### 2. Sebagai Admin/Staff
1. Login sebagai admin atau staff
2. Buka halaman **Konfirmasi & Dokumentasi**
3. Pilih pengajuan yang ingin diverifikasi
4. Lihat komentar dan foto dari peminjam di **Timeline Pengembalian**
5. Balas komentar jika diperlukan (contoh: *"Sudah diterima, terima kasih"*)
6. Jika barang sudah dikembalikan lengkap, klik **✅ Tandai Barang Sudah Dikembalikan Lengkap**
7. Status akan berubah menjadi `done`

## Fitur Timeline
- **Chat-style UI**: Peminjam di kiri (abu-abu), staff/admin di kanan (biru)
- **Photo Upload**: Maksimal 5 MB per foto, disimpan sebagai base64
- **Timestamp**: Setiap komentar memiliki tanggal dan waktu
- **Clickable Photos**: Klik foto untuk melihat ukuran penuh di tab baru
- **Role Badges**: Setiap komentar menampilkan role pengirim (admin/staff/user)

## Dual Mode
Sistem mendukung 2 mode operasi:

### Local Mode (localStorage)
- Tidak perlu backend server
- Data disimpan di browser (localStorage)
- Cocok untuk demo/testing

### API Mode (Backend)
- Membutuhkan server Node.js/Express
- Data disimpan di SQLite database
- Multi-user, persistent storage
- Email notifications

## API Endpoints
Jika menggunakan backend server:

```
GET    /api/comments/:loanId          - Ambil semua komentar untuk loan
POST   /api/comments/:loanId          - Tambah komentar baru
       Body: { message, photoBase64 } - photoBase64 optional
       Auth: Bearer token required

PATCH  /api/comments/:loanId/mark-returned  - Tandai loan selesai
       Auth: Admin/Staff only
```

## Database Schema
Tabel `comments`:
```sql
CREATE TABLE comments (
  id TEXT PRIMARY KEY,
  loanId TEXT NOT NULL,
  userId TEXT,
  userName TEXT NOT NULL,
  userRole TEXT,              -- admin, staff, atau user
  message TEXT NOT NULL,
  photoBase64 TEXT,           -- base64 encoded image
  createdAt TEXT NOT NULL,
  FOREIGN KEY (loanId) REFERENCES loans(id)
);
```

## Troubleshooting

### Foto tidak bisa di-upload
- Pastikan ukuran file ≤ 5 MB
- Format yang didukung: JPG, PNG, GIF, WEBP
- Cek browser console untuk error

### Komentar tidak tersimpan
- **Local Mode**: Cek localStorage browser tidak penuh
- **API Mode**: Pastikan server berjalan di `http://localhost:4000`
- Cek browser console untuk error

### Button "Tandai Dikembalikan" tidak muncul
- Hanya muncul untuk admin/staff
- Hanya untuk loan dengan status `approved`
- Login sebagai admin untuk testing

## Demo Credentials
```
Admin:
  Email: admin@kampus.ac.id
  Password: admin123

Staff:
  Email: staff@kampus.ac.id
  Password: staff123
```

## Tips
✅ Upload foto yang jelas (tidak blur)
✅ Tulis komentar yang deskriptif (lokasi, kondisi barang)
✅ Verifikasi kondisi barang sebelum tandai "Done"
✅ Screenshot timeline untuk dokumentasi

## Next Steps
Fitur yang bisa ditambahkan:
- 🔔 Real-time notifications untuk komentar baru
- 📧 Email notification saat ada komentar
- 📊 Export timeline sebagai PDF
- 🗜️ Image compression untuk hemat storage
- 🎙️ Voice notes untuk komentar
