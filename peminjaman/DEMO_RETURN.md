# 🎬 Demo: Konfirmasi & Dokumentasi Pengembalian

## Skenario Lengkap
Mari kita demo fitur konfirmasi pengembalian dengan skenario nyata.

---

## 📝 Step 1: Buat Peminjaman Baru

1. Buka `borrow.html`
2. Isi form peminjaman:
   ```
   Nama: Budi Santoso
   NIM/NIP: 201234567
   Unit: Fakultas Teknik
   Room Type: Ruang Kelas
   Facility: Ruang 301
   Start Date: [Pilih tanggal H+3]
   Start Time: 09:00
   End Time: 12:00
   Additional: ✓ Infocus (1), ✓ HDMI (2)
   Notes: Untuk kuliah tamu
   ```
3. Klik **Submit Pengajuan**
4. Catat **Kode Booking** yang muncul (contoh: `loan_1234567890`)

---

## ✅ Step 2: Approve Peminjaman (Admin)

1. Login sebagai admin:
   - Email: `admin@kampus.ac.id`
   - Password: `admin123`
2. Buka `admin.html`
3. Tab **Loans Management**
4. Cari loan Budi Santoso
5. Klik **Approve** → **Ya**
6. Status berubah jadi `approved` (hijau)

---

## 📸 Step 3: Upload Foto Pengembalian (Peminjam)

Setelah acara selesai, Budi kembali ke sistem untuk dokumentasi:

1. Buka `confirmation.html`
2. Dari **Riwayat Pengajuan Saya**, klik **Lihat Detail** pada loan Budi
3. Scroll ke **💬 Timeline Pengembalian**
4. Tulis komentar:
   ```
   Barang sudah dikembalikan ke ruang 203.
   Semua dalam kondisi baik.
   ```
5. Klik **📷 Upload Foto Bukti**
6. Pilih foto ruangan/barang yang sudah dikembalikan
7. Preview foto muncul di bawah form
8. Klik **Kirim Komentar**
9. Komentar + foto muncul di timeline (sisi kiri, background abu-abu)

---

## 💬 Step 4: Verifikasi Staff

Staff memeriksa pengembalian:

1. Login sebagai staff:
   - Email: `staff@kampus.ac.id`
   - Password: `staff123`
2. Buka `confirmation.html?id=loan_1234567890`
3. Lihat komentar + foto dari Budi di timeline
4. Klik foto untuk melihat full size
5. Balas komentar:
   ```
   Sudah diterima dengan baik. Terima kasih Pak Budi! 👍
   ```
6. Klik **Kirim Komentar**
7. Balasan staff muncul di kanan (background biru)

---

## ✅ Step 5: Mark Complete (Admin)

Admin memverifikasi dan menutup case:

1. Masih di halaman yang sama
2. Klik **✅ Tandai Barang Sudah Dikembalikan Lengkap**
3. Konfirmasi: **OK**
4. Alert: "✅ Status berhasil diubah menjadi Done"
5. Badge status berubah jadi `done` (hijau)
6. Button "Tandai Dikembalikan" hilang

---

## 🎨 Tampilan Timeline

```
╔════════════════════════════════════════════════════════════╗
║  💬 Timeline Pengembalian                                  ║
╠════════════════════════════════════════════════════════════╣
║                                                            ║
║  ┌─────────────────────────────────────┐                  ║
║  │ Budi Santoso [user]                 │                  ║
║  │ Barang sudah dikembalikan ke ruang  │                  ║
║  │ 203. Semua dalam kondisi baik.      │                  ║
║  │ [Foto: ruang-kelas.jpg]             │                  ║
║  │ 10/01/2025, 14:30                   │                  ║
║  └─────────────────────────────────────┘                  ║
║                                                            ║
║                  ┌─────────────────────────────────────┐  ║
║                  │ Staff Kampus [staff]                │  ║
║                  │ Sudah diterima dengan baik.         │  ║
║                  │ Terima kasih Pak Budi! 👍           │  ║
║                  │ 10/01/2025, 14:45                   │  ║
║                  └─────────────────────────────────────┘  ║
║                                                            ║
╚════════════════════════════════════════════════════════════╝
```

---

## 🧪 Testing Checklist

### Local Mode (tanpa server)
- [ ] Buat loan baru via `borrow.html`
- [ ] Approve loan via `admin.html` (localStorage)
- [ ] Upload foto + komentar di `confirmation.html`
- [ ] Foto tersimpan dan bisa di-klik untuk full size
- [ ] Balas komentar sebagai staff
- [ ] Mark as returned, status jadi `done`
- [ ] Refresh browser, data masih ada (localStorage persist)

### API Mode (dengan server)
- [ ] Start server: `npm run start` di folder `server`
- [ ] Register user baru via `auth.html`
- [ ] Login dan buat loan
- [ ] Admin approve via API
- [ ] Upload foto (max 5MB, cek JSON limit 10mb di server)
- [ ] Verifikasi data tersimpan di SQLite:
  ```powershell
  sqlite3 server/spfk.db "SELECT * FROM comments;"
  ```
- [ ] Multi-user: Budi upload foto, Staff balas dari device lain
- [ ] Mark complete, cek status di database

---

## 🐛 Common Issues

### Foto tidak muncul
**Masalah**: Preview muncul tapi foto hilang setelah submit

**Solusi**: 
```javascript
// Pastikan photoBase64 di-submit ke API
console.log(photoBase64.value); // Harus dimulai dengan "data:image/..."
```

### Timeline kosong
**Masalah**: Komentar tidak muncul setelah submit

**Solusi**:
1. Cek browser console untuk error
2. Verifikasi loanId ada di URL: `confirmation.html?id=loan_xxx`
3. Local mode: Cek `localStorage.getItem('spfk_comments_loan_xxx')`
4. API mode: Cek endpoint `GET /api/comments/loan_xxx`

### Button "Tandai" tidak muncul
**Masalah**: Admin tidak bisa tandai complete

**Solusi**:
```javascript
// Pastikan:
1. User login sebagai admin/staff
2. Loan status = 'approved' (bukan pending/done/rejected)
3. Cek computed: canMarkReturned === true
```

---

## 📊 Database Query Contoh

Cek semua komentar di database:
```sql
sqlite3 server/spfk.db

-- Lihat semua komentar
SELECT 
  c.id, 
  c.userName, 
  c.userRole, 
  substr(c.message, 1, 30) as msg_preview,
  CASE WHEN c.photoBase64 IS NOT NULL THEN 'Yes' ELSE 'No' END as hasPhoto,
  c.createdAt
FROM comments c
ORDER BY c.createdAt DESC;

-- Komentar untuk loan tertentu
SELECT * FROM comments WHERE loanId = 'loan_1234567890';

-- Hitung total foto yang di-upload
SELECT COUNT(*) FROM comments WHERE photoBase64 IS NOT NULL;
```

---

## 🎯 Next: Production Deployment

Sebelum deploy ke production:

1. **Optimasi Foto**
   - Compress image sebelum convert ke base64
   - Limit resolution (contoh: max 1920x1080)
   - Gunakan WEBP format untuk hemat space

2. **Storage External**
   - Upload ke S3/Cloud Storage
   - Simpan URL di database, bukan base64
   - Set lifecycle policy untuk auto-delete old files

3. **Security**
   - Validate file type (hanya image)
   - Scan for malware
   - Set rate limit untuk prevent spam

4. **Monitoring**
   - Log setiap upload foto
   - Alert jika storage usage > 80%
   - Track average photo size

---

**🎉 Selamat! Fitur Konfirmasi & Dokumentasi Pengembalian sudah siap digunakan.**

Untuk pertanyaan lebih lanjut, lihat `RETURN_CONFIRMATION.md` atau `TROUBLESHOOTING.md`.
