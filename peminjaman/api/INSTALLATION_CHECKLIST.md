# ✅ Installation Checklist

Gunakan checklist ini untuk memastikan backend PHP terinstall dengan benar.

## 📋 Pre-Installation

- [ ] XAMPP terinstall
- [ ] Apache running di XAMPP
- [ ] MySQL running di XAMPP
- [ ] PHP versi 7.4+ (cek: `php -v`)
- [ ] Extension PDO dan pdo_mysql aktif

## 📦 File Structure

Pastikan struktur folder lengkap:

```
api/
├── config/
│   ├── config.php ✅
│   ├── database.php ✅
│   ├── config.production.example.php ✅
│   └── database.production.example.php ✅
├── lib/
│   ├── auth.php ✅
│   └── response.php ✅
├── routes/
│   ├── auth.php ✅
│   ├── comments.php ✅
│   ├── facilities.php ✅
│   ├── loans.php ✅
│   └── reports.php ✅
├── uploads/
│   └── .gitkeep ✅
├── .gitignore ✅
├── .htaccess ✅
├── BACKEND_SUMMARY.md ✅
├── database.sql ✅
├── generate_hash.php ✅
├── index.php ✅
├── Postman_Collection.json ✅
├── QUICKSTART.md ✅
├── README.md ✅
└── SETUP_GUIDE.md ✅
```

## 🗄️ Database Setup

- [ ] Database `peminjaman_db` dibuat
- [ ] File `database.sql` diimport
- [ ] Tabel `users` ada dan terisi
- [ ] Tabel `facilities` ada dan terisi
- [ ] Tabel `loans` ada
- [ ] Tabel `loan_facilities` ada
- [ ] Tabel `comments` ada
- [ ] Password hash digenerate dengan `generate_hash.php`
- [ ] Password di tabel `users` diupdate dengan hash yang benar

Cek dengan query:
```sql
SHOW TABLES;
SELECT * FROM users;
SELECT * FROM facilities LIMIT 5;
```

## ⚙️ Configuration

- [ ] File `config/database.php` sudah dikonfigurasi
  - [ ] Host: `localhost`
  - [ ] Database: `peminjaman_db`
  - [ ] Username: `root`
  - [ ] Password: `` (kosong untuk XAMPP default)

- [ ] File `config/config.php` sudah dicek
  - [ ] JWT_SECRET sudah diset
  - [ ] CORS_ORIGIN sesuai kebutuhan
  - [ ] Upload settings sudah benar

- [ ] Folder `uploads/` bisa ditulis (write permission)

## 🧪 API Testing

### 1. Health Check
- [ ] Buka `http://localhost/peminjaman/api/`
- [ ] Response: `{"success": true, "message": "API is running"}`

### 2. Get Facilities (Public)
- [ ] Request: `GET http://localhost/peminjaman/api/facilities`
- [ ] Response berisi array fasilitas
- [ ] Minimal 20+ fasilitas (14 kelas + 8 lainnya)

### 3. Login Test
- [ ] Request: `POST http://localhost/peminjaman/api/auth/login`
- [ ] Body: `{"email": "admin@kampus.ac.id", "password": "admin123"}`
- [ ] Response: `{"success": true, "token": "...", "user": {...}}`
- [ ] Token JWT valid

### 4. Auth Required Endpoint
- [ ] Request: `GET http://localhost/peminjaman/api/auth/me`
- [ ] Header: `Authorization: Bearer <token>`
- [ ] Response berisi data user

### 5. Admin Endpoint
- [ ] Login sebagai admin
- [ ] Request: `POST http://localhost/peminjaman/api/facilities`
- [ ] Header: `Authorization: Bearer <admin-token>`
- [ ] Body: data fasilitas baru
- [ ] Response: fasilitas berhasil dibuat

### 6. File Upload Test (Optional)
- [ ] Login dan dapat token
- [ ] Create loan dan dapat loan ID
- [ ] POST ke `/api/comments/:loanId` dengan form-data
- [ ] Include field `comment` dan file `photos[]`
- [ ] File berhasil diupload ke folder `uploads/`

## 🖥️ Frontend Integration

- [ ] File HTML frontend ada (auth.html, borrow.html, dll)
- [ ] URL API di JavaScript sudah diupdate:
  ```javascript
  const API = 'http://localhost/peminjaman/api';
  ```
- [ ] Login dari `auth.html` berfungsi
- [ ] Form peminjaman di `borrow.html` berfungsi
- [ ] Admin panel di `admin.html` berfungsi

## 🔐 Security Check

- [ ] Password di database ter-hash (bcrypt)
- [ ] JWT token expires setelah 7 hari
- [ ] Admin endpoints tidak bisa diakses user biasa
- [ ] SQL injection protected (prepared statements)
- [ ] File upload hanya terima image
- [ ] Max file size 5MB

## 📊 Postman Testing

- [ ] Import `Postman_Collection.json`
- [ ] Test endpoint "Health Check" ✅
- [ ] Test "Auth > Login" dan save token ✅
- [ ] Test "Facilities > List Facilities" ✅
- [ ] Test "Loans > Create Loan" dengan token ✅
- [ ] Test "Reports > Summary" dengan admin token ✅

## 🐛 Troubleshooting

### Jika API tidak bisa diakses:
- [ ] Cek Apache running
- [ ] Cek error log: `C:\xampp\apache\logs\error.log`
- [ ] Cek mod_rewrite aktif di Apache
- [ ] Cek .htaccess ada di folder api/

### Jika database error:
- [ ] Cek MySQL running
- [ ] Cek koneksi di config/database.php
- [ ] Cek database peminjaman_db ada
- [ ] Cek tabel-tabel sudah dibuat

### Jika login gagal:
- [ ] Generate password hash: `php generate_hash.php`
- [ ] Update password di database
- [ ] Cek email dan password benar

### Jika CORS error:
- [ ] Update CORS_ORIGIN di config/config.php
- [ ] Restart Apache
- [ ] Clear browser cache

## ✅ Final Verification

Jalankan semua test ini untuk memastikan backend siap production:

1. **Auth Flow**
   - [ ] Register user baru
   - [ ] Login dan dapat token
   - [ ] Access protected endpoint dengan token
   - [ ] Token expired setelah 7 hari

2. **CRUD Operations**
   - [ ] Create facility (admin)
   - [ ] Read facilities (public)
   - [ ] Update facility (admin)
   - [ ] Delete facility (admin)

3. **Loan Flow**
   - [ ] User create loan
   - [ ] Admin/Staff approve loan
   - [ ] User add comment
   - [ ] Admin/Staff mark as returned

4. **Reports**
   - [ ] Admin view summary statistics
   - [ ] User view own statistics
   - [ ] Admin view facility usage

## 🎉 Success!

Jika semua checklist di atas ✅, backend PHP Anda sudah siap digunakan!

**Next Steps:**
1. Integrasikan dengan frontend
2. Test semua fitur end-to-end
3. Deploy ke production (optional)
4. Setup backup database rutin

---

**Selamat! Backend PHP berhasil terinstall!** 🚀
