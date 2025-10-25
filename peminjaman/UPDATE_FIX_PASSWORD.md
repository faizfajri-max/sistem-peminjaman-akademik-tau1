# 🔧 UPDATE: Fix Password & Kelola Users

## 📝 Perubahan

### 1. ✅ **Password Login Diperbaiki**
Password hash di database telah diperbaiki menggunakan bcrypt yang valid.

**Kredensial Login:**
- 👨‍💼 **Admin**: `admin@kampus.ac.id` / `admin123`
- 👨‍💻 **Staff**: `staff@kampus.ac.id` / `admin123`
- 👤 **User**: `user@kampus.ac.id` / `admin123`

### 2. 👥 **Fitur Kelola Users (Admin Only)**
Admin sekarang bisa mengelola users langsung dari Admin Panel:
- ✅ Lihat semua users
- ✅ Ubah role user (admin/staff/user)
- ✅ Hapus user
- ✅ Search & filter user by role
- ✅ Pagination

### 3. 📊 **Data Peminjaman Lengkap**
Database sekarang memiliki data sample peminjaman yang lengkap:
- **4 Approved** - Peminjaman yang disetujui
- **3 Pending** - Menunggu approval
- **3 Done** - Sudah selesai
- **2 Rejected** - Ditolak
- **Total: 12 peminjaman** dengan berbagai status

---

## 🚀 Cara Update Database

### **Opsi 1: Update Database Existing** (Recommended)

Jika sudah punya database `peminjaman_db`:

```sql
-- Buka phpMyAdmin
-- 1. http://localhost/phpmyadmin
-- 2. Pilih database: peminjaman_db
-- 3. Tab "SQL"
-- 4. Copy-paste isi file: database-update.sql
-- 5. Klik "Go"
```

File: `api/database-update.sql`

### **Opsi 2: Rebuild Database dari Awal**

```sql
-- Di phpMyAdmin:
-- 1. Drop database: peminjaman_db (jika ada)
-- 2. Import file: api/database.sql
-- 3. Selesai!
```

### **Opsi 3: Update Manual Password**

Jika hanya ingin fix password:

```sql
UPDATE users 
SET password = '$2y$10$e0MYzXyjpJS7Pd6hUq.LCOCj4vJs0rg4wEhWzFLcNjC.6NKrTH1Eq' 
WHERE email IN ('admin@kampus.ac.id', 'staff@kampus.ac.id', 'user@kampus.ac.id');
```

---

## 🆕 Fitur Kelola Users di Admin Panel

### **Akses:**
1. Login sebagai **Admin**: http://localhost/peminjaman/admin-login.html
2. Klik tab **"Users"** di Admin Panel
3. Lihat, edit role, atau hapus users

### **Fitur:**

#### 1. **Lihat Semua Users**
- Tabel lengkap dengan ID, nama, email, role, tanggal daftar
- Pagination (20 users per halaman)

#### 2. **Edit Role User** (Dropdown)
- Ubah role: Admin, Staff, atau User
- Auto-save saat dropdown berubah
- Confirm dialog sebelum update
- **Tidak bisa ubah role sendiri** (proteksi)

#### 3. **Hapus User**
- Button merah "Hapus"
- Confirm dialog + warning
- **Tidak bisa hapus diri sendiri** (proteksi)
- Cascade delete: peminjaman & comments user ikut terhapus

#### 4. **Search & Filter**
- Search by nama atau email
- Filter by role (Admin/Staff/User)
- Real-time search

---

## 📡 API Endpoints Baru

### **1. GET `/api/users`**
List semua users (admin/staff only)

**Query Parameters:**
- `page` - Halaman (default: 1)
- `limit` - Per halaman (default: 20)
- `search` - Cari nama/email
- `role` - Filter by role (admin/staff/user)

**Response:**
```json
{
  "success": true,
  "users": [
    {
      "id": 1,
      "name": "Administrator",
      "email": "admin@kampus.ac.id",
      "role": "admin",
      "created_at": "2025-10-24 10:00:00",
      "updated_at": "2025-10-24 10:00:00"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 20,
    "total": 6,
    "totalPages": 1
  }
}
```

---

### **2. PUT `/api/users/:id/role`**
Update role user (admin only)

**Request Body:**
```json
{
  "role": "staff"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Role user berhasil diubah",
  "user": {
    "id": 4,
    "name": "Budi Santoso",
    "email": "budi@student.kampus.ac.id",
    "role": "staff"
  }
}
```

**Validasi:**
- ❌ Admin tidak bisa ubah role sendiri (403)
- ❌ User tidak ditemukan (404)
- ❌ Role tidak valid (400)

---

### **3. DELETE `/api/users/:id`**
Hapus user (admin only)

**Response:**
```json
{
  "success": true,
  "message": "User berhasil dihapus",
  "user": {
    "id": 4,
    "name": "Budi Santoso",
    "email": "budi@student.kampus.ac.id"
  }
}
```

**Cascade Delete:**
- Semua loans user → dihapus
- Semua comments user → dihapus
- Semua loan_facilities terkait → dihapus

**Validasi:**
- ❌ Admin tidak bisa hapus diri sendiri (403)
- ❌ User tidak ditemukan (404)

---

## 🧪 Testing

### **Test 1: Login dengan Password Baru**
```
1. Buka: http://localhost/peminjaman/admin-login.html
2. Email: admin@kampus.ac.id
3. Password: admin123
4. ✅ Berhasil masuk ke admin panel
```

### **Test 2: Kelola Users**
```
1. Login sebagai admin
2. Klik tab "Users"
3. ✅ Lihat list users
4. Ubah role user → ✅ Berhasil
5. Search "budi" → ✅ Filter bekerja
6. Hapus user test → ✅ Berhasil
```

### **Test 3: Proteksi Admin**
```
1. Login sebagai admin
2. Coba ubah role sendiri → ❌ Disabled
3. Coba hapus diri sendiri → ❌ Disabled
4. ✅ Proteksi bekerja
```

### **Test 4: Filter & Pagination**
```
1. Filter by role "admin" → ✅ Hanya admin
2. Search "student" → ✅ Filter email
3. Page navigation → ✅ Bekerja (jika >20 users)
```

---

## 📁 File yang Diubah/Ditambah

### **File Baru:**
1. ✨ `api/database-update.sql` - SQL update password & data
2. ✨ `api/generate-password.php` - Tool generate hash password
3. ✨ `UPDATE_FIX_PASSWORD.md` - Dokumentasi ini

### **File Diubah:**
1. 📝 `api/database.sql` - Update password hash yang benar
2. 📝 `api/routes/auth.php` - Tambah methods: getUsers(), updateUserRole(), deleteUser()
3. 📝 `api/index.php` - Tambah routing /api/users
4. 📝 `admin.html` - Tambah tab "Users" dan UI kelola users

---

## 🔐 Keamanan

### **Proteksi yang Diterapkan:**

1. **Role-based Access Control**
   - GET /users → Admin & Staff only
   - PUT /users/:id/role → Admin only
   - DELETE /users/:id → Admin only

2. **Self-protection**
   - Admin tidak bisa ubah role sendiri
   - Admin tidak bisa hapus akun sendiri
   - Button disabled di UI untuk aksi sendiri

3. **Validation**
   - Role harus: admin, staff, atau user
   - User must exist
   - Token JWT valid

4. **Cascade Delete**
   - Foreign key ON DELETE CASCADE
   - Data terkait otomatis terhapus
   - Prevent orphan data

---

## 🛠️ Tools Tambahan

### **Generate Password Hash**

Buka: http://localhost/peminjaman/api/generate-password.php

Tool ini akan:
- ✅ Generate hash baru setiap reload
- ✅ Tampilkan SQL update query
- ✅ Test verify password
- ✅ Info lengkap cara pakai

**Screenshot:**
```
🔐 Generate Password Hash

Admin
Email: admin@kampus.ac.id
Password: admin123
Hash: $2y$10$e0MYzXyjpJS7Pd6hUq.LCOCj4vJs0rg4wEhWzFLcNjC.6NKrTH1Eq

SQL Update Query:
UPDATE users SET password = '$2y$10$...' WHERE email = 'admin@kampus.ac.id';
```

---

## 📊 Database Structure Update

### **Users Table** (Tidak berubah)
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- bcrypt hash
    role ENUM('admin', 'staff', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### **Sample Data (6 Users):**
| ID | Name | Email | Role | Password |
|----|------|-------|------|----------|
| 1 | Administrator | admin@kampus.ac.id | admin | admin123 |
| 2 | Staff Akademik | staff@kampus.ac.id | staff | admin123 |
| 3 | User Demo | user@kampus.ac.id | user | admin123 |
| 4 | Budi Santoso | budi@student.kampus.ac.id | user | admin123 |
| 5 | Ani Wijaya | ani@student.kampus.ac.id | user | admin123 |
| 6 | Dedi Prasetyo | dedi@student.kampus.ac.id | user | admin123 |

---

## 🎯 Use Cases

### **Use Case 1: Promote User ke Staff**
```
Admin ingin mengangkat Budi menjadi Staff:

1. Admin login
2. Buka tab "Users"
3. Cari "Budi Santoso"
4. Ubah role dropdown: User → Staff
5. Confirm "Ubah role Budi Santoso menjadi staff?"
6. ✅ Budi sekarang Staff, bisa akses Admin Panel
```

### **Use Case 2: Hapus User yang Tidak Aktif**
```
Admin ingin hapus user yang tidak aktif:

1. Admin login
2. Buka tab "Users"
3. Klik "Hapus" di user target
4. Confirm: "Hapus user X? Semua peminjaman akan ikut terhapus."
5. ✅ User dihapus beserta semua datanya
```

### **Use Case 3: Audit Users**
```
Admin ingin lihat siapa saja yang terdaftar:

1. Admin login
2. Buka tab "Users"
3. Filter by role "admin" → Lihat semua admin
4. Filter by role "staff" → Lihat semua staff
5. Search "student" → Lihat mahasiswa
6. ✅ Audit lengkap
```

---

## ⚠️ Catatan Penting

### **Sebelum Update:**
1. ✅ Backup database lama (export via phpMyAdmin)
2. ✅ Pastikan XAMPP Apache & MySQL running
3. ✅ Test di environment dev dulu

### **Setelah Update:**
1. ✅ Verify login dengan password baru
2. ✅ Test kelola users di admin panel
3. ✅ Cek data peminjaman lengkap

### **Production:**
1. ⚠️ Ganti password default "admin123"
2. ⚠️ Gunakan password yang kuat
3. ⚠️ Jangan share password hash ke publik
4. ⚠️ Enable HTTPS untuk security

---

## 🐛 Troubleshooting

### **Problem 1: Login Gagal "Email atau password salah"**

**Solution:**
```sql
-- 1. Cek password hash di database
SELECT email, password FROM users WHERE email = 'admin@kampus.ac.id';

-- 2. Jika hash berbeda, update:
UPDATE users SET password = '$2y$10$e0MYzXyjpJS7Pd6hUq.LCOCj4vJs0rg4wEhWzFLcNjC.6NKrTH1Eq' 
WHERE email = 'admin@kampus.ac.id';

-- 3. Atau jalankan: database-update.sql
```

### **Problem 2: Tab "Users" Tidak Muncul**

**Solution:**
- Pastikan login sebagai **admin** (bukan staff/user)
- Tab "Users" hanya untuk admin
- Cek role di topbar admin panel

### **Problem 3: "Fitur kelola users hanya tersedia dengan backend API"**

**Solution:**
- Pastikan backend PHP running
- Test: http://localhost/peminjaman/api/
- Jika offline, fitur users tidak available (butuh database real)

### **Problem 4: Update Role Tidak Berhasil**

**Solution:**
```javascript
// Cek console browser (F12)
// Error: "Tidak dapat mengubah role sendiri"
// → Ini proteksi, tidak bisa ubah role sendiri

// Error: "Gagal mengubah role: 401"
// → Token expired, logout & login ulang
```

---

## 📞 Support

Jika ada masalah:
1. Buka Console browser (F12)
2. Cek error messages
3. Verify backend running: http://localhost/peminjaman/api/
4. Cek database: phpMyAdmin → peminjaman_db → users

---

**Update Date**: October 24, 2025  
**Version**: 1.3.0  
**Status**: ✅ Production Ready

---

## 🎉 Summary

✅ **Password login diperbaiki** - Gunakan `admin123` untuk semua akun  
✅ **Fitur kelola users** - Admin bisa ubah role & hapus user  
✅ **Data peminjaman lengkap** - 12 sample data dengan berbagai status  
✅ **3 endpoint API baru** - GET /users, PUT /users/:id/role, DELETE /users/:id  
✅ **Security proteksi** - Tidak bisa edit/hapus diri sendiri  
✅ **Tool generate password** - Helper untuk testing

**Login sekarang:**
http://localhost/peminjaman/admin-login.html  
Email: `admin@kampus.ac.id`  
Password: `admin123`

Selamat mencoba! 🚀
