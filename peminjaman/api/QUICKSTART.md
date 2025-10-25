# 🚀 Quick Start - Backend PHP

Panduan cepat untuk menjalankan backend PHP dalam 5 menit!

## Langkah Cepat

### 1️⃣ Start XAMPP
- Buka XAMPP Control Panel
- Klik **Start** pada Apache dan MySQL

### 2️⃣ Import Database
- Buka browser: `http://localhost/phpmyadmin`
- Klik tab **SQL**
- Copy paste isi file `database.sql`
- Klik **Go**

### 3️⃣ Generate Password Hash
```powershell
cd c:\xamppp\htdocs\peminjaman\api
php generate_hash.php
```

Copy hash yang dihasilkan dan jalankan di phpMyAdmin:
```sql
UPDATE users SET password = '<hash>' WHERE email = 'admin@kampus.ac.id';
```

### 4️⃣ Test API
Buka browser: `http://localhost/peminjaman/api/`

Jika muncul:
```json
{
  "success": true,
  "message": "API is running"
}
```

**Selamat! Backend sudah berjalan! 🎉**

### 5️⃣ Login Test
Buka: `http://localhost/peminjaman/auth.html`

Atau gunakan curl:
```powershell
curl -X POST http://localhost/peminjaman/api/auth/login `
  -H "Content-Type: application/json" `
  -d '{\"email\":\"admin@kampus.ac.id\",\"password\":\"admin123\"}'
```

## Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@kampus.ac.id | admin123 |
| Staff | staff@kampus.ac.id | staff123 |
| User | user@kampus.ac.id | user123 |

## Endpoint Penting

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/` | Health check |
| POST | `/api/auth/login` | Login |
| GET | `/api/facilities` | List fasilitas |
| GET | `/api/loans` | List peminjaman |
| POST | `/api/loans` | Buat peminjaman |

## Testing dengan Postman

Import file `Postman_Collection.json` ke Postman untuk testing semua endpoint.

## Troubleshooting

**Error "Connection error"?**
- Pastikan MySQL running di XAMPP
- Cek `api/config/database.php`

**Error "Table doesn't exist"?**
- Import ulang `database.sql`

**Login gagal?**
- Generate password hash dengan `generate_hash.php`
- Update di database

## Next Steps

✅ Baca [SETUP_GUIDE.md](SETUP_GUIDE.md) untuk detail lengkap
✅ Baca [README.md](README.md) untuk dokumentasi API
✅ Integrasikan dengan frontend HTML yang ada

---

**Happy coding! 💻**
