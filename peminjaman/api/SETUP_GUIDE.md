# 📘 Panduan Setup Backend PHP - Sistem Peminjaman Fasilitas

Panduan lengkap untuk setup dan menjalankan backend PHP untuk Sistem Peminjaman Fasilitas Kampus.

## 🎯 Langkah-langkah Setup

### 1. Persiapan XAMPP

1. Pastikan XAMPP sudah terinstall
2. Buka XAMPP Control Panel
3. Start **Apache** dan **MySQL**

### 2. Setup Database

#### Cara 1: Menggunakan phpMyAdmin (Recommended untuk pemula)

1. Buka browser dan akses: `http://localhost/phpmyadmin`
2. Klik tab **"SQL"** di bagian atas
3. Copy seluruh isi file `api/database.sql`
4. Paste ke text area dan klik **"Go"**
5. Database `peminjaman_db` akan otomatis dibuat beserta tabelnya

#### Cara 2: Menggunakan Command Line

```powershell
# Buka PowerShell
cd c:\xamppp\htdocs\peminjaman

# Import database
c:\xampp\mysql\bin\mysql.exe -u root -p < api\database.sql

# Jika diminta password, tekan Enter (default XAMPP tidak ada password)
```

### 3. Generate Password Hash

Backend ini menggunakan bcrypt untuk hash password. Generate hash untuk password default:

```powershell
cd c:\xamppp\htdocs\peminjaman\api
php generate_hash.php
```

Copy hash yang dihasilkan dan update di database:

```sql
-- Buka phpMyAdmin > peminjaman_db > users > Browse
-- Atau jalankan SQL berikut:

UPDATE users SET password = '<hash-untuk-admin123>' WHERE email = 'admin@kampus.ac.id';
UPDATE users SET password = '<hash-untuk-staff123>' WHERE email = 'staff@kampus.ac.id';
UPDATE users SET password = '<hash-untuk-user123>' WHERE email = 'user@kampus.ac.id';
```

### 4. Konfigurasi Backend

Edit file `api/config/database.php` jika perlu mengubah konfigurasi database:

```php
private $host = "localhost";        // Host MySQL
private $db_name = "peminjaman_db"; // Nama database
private $username = "root";          // Username MySQL
private $password = "";              // Password MySQL (kosong untuk XAMPP default)
```

### 5. Update JWT Secret (Optional tapi direkomendasikan)

Edit file `api/config/config.php`:

```php
define('JWT_SECRET', 'ganti-dengan-string-random-yang-panjang-dan-kuat');
```

Generate string random yang kuat untuk JWT secret.

### 6. Test API

Buka browser dan akses:

```
http://localhost/peminjaman/api/
```

Jika berhasil, Anda akan melihat response:

```json
{
  "success": true,
  "message": "API is running",
  "timestamp": "2025-10-24 12:34:56"
}
```

### 7. Test Login

Gunakan tools seperti **Postman** atau **curl** untuk test login:

#### Menggunakan PowerShell (curl):

```powershell
curl -X POST http://localhost/peminjaman/api/auth/login `
  -H "Content-Type: application/json" `
  -d '{\"email\":\"admin@kampus.ac.id\",\"password\":\"admin123\"}'
```

#### Atau gunakan file HTML yang sudah ada:

Buka `http://localhost/peminjaman/auth.html` di browser dan coba login dengan:
- Email: `admin@kampus.ac.id`
- Password: `admin123`

## 🔧 Konfigurasi Frontend

Update file JavaScript di frontend untuk menggunakan backend PHP:

Edit file `assets/js/app.js` atau file HTML yang relevan:

```javascript
// Ganti URL API dari Node.js ke PHP
const API = 'http://localhost/peminjaman/api';

// Pastikan useApi diset true jika backend tersedia
let useApi = true;
```

Atau di `auth.html`, sudah ada kode yang otomatis detect backend:

```javascript
const API = 'http://localhost:4000/api';  // Ganti menjadi
const API = 'http://localhost/peminjaman/api';
```

## 📋 Checklist Setup

- [ ] XAMPP Apache running
- [ ] XAMPP MySQL running
- [ ] Database `peminjaman_db` dibuat
- [ ] Tabel-tabel dibuat (users, facilities, loans, dll)
- [ ] Data seed diimport (users, facilities default)
- [ ] Password hash digenerate dan diupdate
- [ ] File konfigurasi dicek (database.php, config.php)
- [ ] Folder `api/uploads/` ada dan bisa ditulis
- [ ] API health check berjalan (`http://localhost/peminjaman/api/`)
- [ ] Login berhasil dengan akun default

## 🧪 Testing Endpoints

### 1. Health Check
```
GET http://localhost/peminjaman/api/
```

### 2. Register User Baru
```
POST http://localhost/peminjaman/api/auth/register
Content-Type: application/json

{
  "name": "Test User",
  "email": "test@example.com",
  "password": "password123"
}
```

### 3. Login
```
POST http://localhost/peminjaman/api/auth/login
Content-Type: application/json

{
  "email": "admin@kampus.ac.id",
  "password": "admin123"
}
```

### 4. Get Facilities (Public)
```
GET http://localhost/peminjaman/api/facilities
```

### 5. Get Loans (dengan filter)
```
GET http://localhost/peminjaman/api/loans?status=pending
```

### 6. Create Loan (Requires Auth)
```
POST http://localhost/peminjaman/api/loans
Authorization: Bearer <your-token>
Content-Type: application/json

{
  "unit": "Fakultas Teknik",
  "purpose": "Seminar Workshop",
  "start_date": "2025-10-30",
  "end_date": "2025-10-30",
  "start_time": "09:00",
  "end_time": "16:00",
  "room_type": "Kelas",
  "participants": 50,
  "facilities": [1, 2]
}
```

## 🐛 Troubleshooting

### Error: "Connection error"

**Penyebab**: MySQL tidak running atau konfigurasi database salah

**Solusi**:
1. Pastikan MySQL di XAMPP sudah running
2. Cek konfigurasi di `api/config/database.php`
3. Pastikan database `peminjaman_db` sudah dibuat

### Error: "Table doesn't exist"

**Penyebab**: Database belum diimport

**Solusi**:
1. Import ulang file `api/database.sql`
2. Cek di phpMyAdmin apakah tabel-tabel sudah ada

### Error: "Email atau password salah"

**Penyebab**: Password hash belum digenerate atau salah

**Solusi**:
1. Jalankan `php generate_hash.php`
2. Update password hash di database
3. Atau gunakan password hash default yang ada di database.sql

### Error 404 di semua endpoint

**Penyebab**: Apache mod_rewrite tidak aktif atau .htaccess tidak bekerja

**Solusi**:
1. Buka `C:\xampp\apache\conf\httpd.conf`
2. Cari dan uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Cari `<Directory "C:/xampp/htdocs">` dan ubah `AllowOverride None` menjadi `AllowOverride All`
4. Restart Apache

### Upload foto tidak berfungsi

**Penyebab**: Folder uploads tidak bisa ditulis

**Solusi**:
1. Pastikan folder `api/uploads/` ada
2. Di Windows, klik kanan folder > Properties > Security
3. Pastikan user memiliki permission Write

### CORS Error di Frontend

**Penyebab**: CORS tidak dikonfigurasi dengan benar

**Solusi**:
1. Edit `api/config/config.php`
2. Ubah `define('CORS_ORIGIN', '*');` atau sesuaikan dengan domain frontend
3. Pastikan header CORS diset di `api/index.php`

## 📱 Integrasi dengan Frontend yang Ada

Backend PHP ini dirancang untuk kompatibel dengan frontend yang sudah ada. Update file-file berikut:

### File `auth.html`:
```javascript
// Ganti baris:
const API = 'http://localhost:4000/api';

// Menjadi:
const API = 'http://localhost/peminjaman/api';
```

### File `borrow.html`, `admin.html`, dll:
Lakukan hal yang sama, ganti URL API Node.js menjadi PHP.

## 🚀 Next Steps

Setelah setup berhasil:

1. ✅ Test semua endpoint dengan Postman
2. ✅ Integrasi dengan frontend HTML
3. ✅ Test fitur upload foto di comments
4. ✅ Test role-based access (admin, staff, user)
5. ✅ Deploy ke server production (optional)

## 📚 Resources

- [PHP Official Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [JWT.io](https://jwt.io/) - JWT Debugger
- [XAMPP Documentation](https://www.apachefriends.org/docs/)

## 💡 Tips

- Gunakan Postman atau Thunder Client (VS Code extension) untuk test API
- Enable error reporting di PHP untuk debugging: `ini_set('display_errors', 1);`
- Backup database secara berkala
- Ganti JWT secret key di production
- Gunakan HTTPS di production

## 📞 Support

Jika mengalami masalah:
1. Cek console/error log di XAMPP
2. Cek error log PHP di `C:\xampp\php\logs\php_error_log`
3. Cek Apache error log di `C:\xampp\apache\logs\error.log`

---

**Selamat! Backend PHP Anda sudah siap digunakan! 🎉**
