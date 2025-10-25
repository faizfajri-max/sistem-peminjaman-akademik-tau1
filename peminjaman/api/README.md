# Backend PHP - Sistem Peminjaman Fasilitas Kampus

Backend REST API menggunakan PHP murni (tanpa framework) untuk Sistem Peminjaman Fasilitas Kampus.

## 🚀 Fitur

- ✅ Authentication dengan JWT (JSON Web Token)
- ✅ CRUD Fasilitas (Kelas, Ballroom, Peralatan, dll)
- ✅ Manajemen Peminjaman (Create, Read, Update Status, Delete)
- ✅ Dokumentasi Pengembalian dengan Upload Foto
- ✅ Laporan dan Statistik
- ✅ Role-based Access Control (Admin, Staff, User)
- ✅ CORS Support untuk integrasi frontend

## 📋 Requirements

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- XAMPP atau server lokal lainnya
- Extension PHP yang diperlukan:
  - PDO
  - pdo_mysql
  - mbstring
  - json

## 🔧 Instalasi

### 1. Setup Database

```powershell
# Akses MySQL melalui phpMyAdmin atau command line
# Import file database.sql
mysql -u root -p < api/database.sql
```

Atau buka phpMyAdmin dan import file `api/database.sql`

### 2. Konfigurasi Database

Edit file `api/config/database.php` sesuaikan dengan pengaturan MySQL Anda:

```php
private $host = "localhost";
private $db_name = "peminjaman_db";
private $username = "root";
private $password = "";
```

### 3. Set Permissions untuk Upload Folder

```powershell
# Di Windows, folder uploads harus bisa ditulis
# Cek bahwa folder api/uploads/ ada dan bisa ditulis
```

### 4. Update JWT Secret (Optional tapi direkomendasikan)

Edit file `api/config/config.php`:

```php
define('JWT_SECRET', 'ganti-dengan-string-rahasia-yang-kuat');
```

### 5. Jalankan Server

Jika menggunakan XAMPP, pastikan Apache dan MySQL sudah running.

Akses API di: `http://localhost/peminjaman/api/`

## 📚 API Endpoints

### Health Check
- `GET /api/` atau `GET /api/health` - Cek status API

### Authentication
- `POST /api/auth/register` - Registrasi user baru
- `POST /api/auth/login` - Login dan dapatkan token JWT
- `GET /api/auth/me` - Informasi user saat ini (requires auth)

### Facilities
- `GET /api/facilities` - List semua fasilitas
- `GET /api/facilities/:id` - Detail fasilitas
- `POST /api/facilities` - Tambah fasilitas (admin only)
- `PUT /api/facilities/:id` - Update fasilitas (admin only)
- `DELETE /api/facilities/:id` - Hapus fasilitas (admin only)

### Loans (Peminjaman)
- `GET /api/loans` - List peminjaman (filter: status, userId, from, to, roomType)
- `GET /api/loans/:id` - Detail peminjaman
- `POST /api/loans` - Buat peminjaman baru (requires auth)
- `PATCH /api/loans/:id/status` - Update status (admin/staff only)
- `DELETE /api/loans/:id` - Hapus peminjaman (owner atau admin)

### Comments (Dokumentasi Pengembalian)
- `GET /api/comments/:loanId` - List komentar untuk peminjaman
- `POST /api/comments/:loanId` - Tambah komentar + foto (requires auth)
- `PATCH /api/comments/:loanId/mark-returned` - Tandai selesai (admin/staff)
- `DELETE /api/comments/:commentId` - Hapus komentar (owner atau admin)

### Reports
- `GET /api/reports/summary` - Statistik keseluruhan (admin/staff only)
- `GET /api/reports/user-stats` - Statistik per user (requires auth)
- `GET /api/reports/facility-usage` - Laporan penggunaan fasilitas (admin/staff)

## 🔐 Authentication

Setelah login, gunakan token JWT di header:

```
Authorization: Bearer <your-jwt-token>
```

### Akun Default

Setelah import database, gunakan akun berikut untuk testing:

- **Admin**: `admin@kampus.ac.id` / `admin123`
- **Staff**: `staff@kampus.ac.id` / `staff123`
- **User**: `user@kampus.ac.id` / `user123`

## 📝 Contoh Request

### Login

```bash
curl -X POST http://localhost/peminjaman/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@kampus.ac.id",
    "password": "admin123"
  }'
```

Response:
```json
{
  "success": true,
  "message": "Login berhasil",
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@kampus.ac.id",
    "role": "admin"
  },
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Buat Peminjaman

```bash
curl -X POST http://localhost/peminjaman/api/loans \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{
    "unit": "Fakultas Teknik",
    "purpose": "Seminar Workshop",
    "start_date": "2025-10-30",
    "end_date": "2025-10-30",
    "start_time": "09:00",
    "end_time": "16:00",
    "room_type": "Kelas",
    "participants": 50,
    "facilities": [1, 2]
  }'
```

### Upload Komentar dengan Foto

```bash
curl -X POST http://localhost/peminjaman/api/comments/1 \
  -H "Authorization: Bearer <token>" \
  -F "comment=Fasilitas dalam kondisi baik" \
  -F "photos[]=@/path/to/photo1.jpg" \
  -F "photos[]=@/path/to/photo2.jpg"
```

## 🗂️ Struktur Folder

```
api/
├── config/
│   ├── config.php          # Konfigurasi umum
│   └── database.php        # Konfigurasi database
├── lib/
│   ├── auth.php           # JWT & Auth helpers
│   └── response.php       # Response helpers
├── routes/
│   ├── auth.php           # Auth endpoints
│   ├── facilities.php     # Facilities endpoints
│   ├── loans.php          # Loans endpoints
│   ├── comments.php       # Comments endpoints
│   └── reports.php        # Reports endpoints
├── uploads/               # Folder untuk upload foto
├── database.sql          # SQL schema & seed data
├── index.php            # Entry point & router
├── .htaccess            # Apache rewrite rules
└── README.md            # Dokumentasi ini
```

## 🐛 Troubleshooting

### Error: "Connection error"
- Pastikan MySQL sudah running
- Cek konfigurasi database di `config/database.php`
- Pastikan database `peminjaman_db` sudah dibuat

### Error: "Unauthorized"
- Pastikan token JWT dikirim di header Authorization
- Cek apakah token masih valid (belum expired)

### Upload foto tidak berfungsi
- Pastikan folder `api/uploads/` ada dan bisa ditulis
- Cek permission folder

### CORS Error
- Update `CORS_ORIGIN` di `config/config.php`
- Pastikan frontend mengirim request dengan header yang benar

## 📱 Integrasi dengan Frontend

Update konfigurasi di frontend (file HTML) untuk menggunakan backend PHP:

```javascript
const API = 'http://localhost/peminjaman/api';
```

Backend ini kompatibel dengan frontend yang sudah ada (auth.html, borrow.html, dll).

## 🔒 Security Notes

- **Ganti JWT_SECRET** di production dengan string yang kuat
- Password di-hash menggunakan `password_hash()` dengan bcrypt
- Gunakan HTTPS di production
- Validasi semua input dari user
- Implementasi rate limiting untuk production

## 📄 License

MIT License - Free to use for educational purposes

## 👨‍💻 Developer

Dibuat untuk keperluan sistem peminjaman fasilitas kampus
