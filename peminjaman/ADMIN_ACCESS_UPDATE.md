# Update: Admin Panel Access Control

## 📝 Perubahan

Sistem login telah diupdate dengan pemisahan alur akses untuk Admin/Staff dan User biasa.

## 🎯 Fitur Baru

### 1. **Redirect Otomatis Berdasarkan Role**

Saat login, pengguna akan otomatis diarahkan ke halaman yang sesuai:

- **Admin / Staff** → Langsung ke `admin.html` (Admin Panel)
- **User biasa** → Ke `index.html` (Beranda)

### 2. **Proteksi Admin Panel**

Admin Panel (`admin.html`) sekarang memiliki proteksi berlapis:

- ✅ Hanya bisa diakses oleh user dengan role `admin` atau `staff`
- ✅ Cek otomatis saat halaman dimuat
- ✅ Redirect ke login jika tidak punya akses
- ✅ Alert notifikasi jika akses ditolak

### 3. **Support Backend PHP**

- ✅ Auth system otomatis coba backend PHP terlebih dahulu
- ✅ Fallback ke backend Node.js jika PHP tidak tersedia
- ✅ Fallback ke localStorage jika semua backend tidak tersedia

## 🔐 Akun & Role

### Default Accounts (setelah import database.sql)

| Role | Email | Password | Akses |
|------|-------|----------|-------|
| **Admin** | admin@kampus.ac.id | admin123 | ✅ Admin Panel + Full Access |
| **Staff** | staff@kampus.ac.id | staff123 | ✅ Admin Panel (terbatas) |
| **User** | user@kampus.ac.id | user123 | ❌ Beranda saja |

## 📋 Alur Login Baru

```
User membuka auth.html
      ↓
Input email & password
      ↓
Klik "Masuk"
      ↓
System cek role
      ↓
      ├─→ Admin/Staff → admin.html
      └─→ User biasa → index.html
```

## 🛠️ Fitur Admin Panel

### Admin (Full Access)
- ✅ Lihat semua pengajuan peminjaman
- ✅ Approve/Reject peminjaman
- ✅ CRUD Fasilitas (Tambah, Edit, Hapus)
- ✅ Lihat laporan & statistik
- ✅ Manage semua data

### Staff (Limited Access)
- ✅ Lihat semua pengajuan peminjaman
- ✅ Approve/Reject peminjaman
- ✅ Lihat laporan & statistik
- ❌ Tidak bisa tambah/edit/hapus fasilitas

### User (No Access)
- ❌ Tidak bisa akses Admin Panel
- ✅ Hanya bisa ajukan peminjaman
- ✅ Lihat status peminjaman sendiri

## 🔄 Testing

### Test 1: Login sebagai Admin
```
1. Buka http://localhost/peminjaman/auth.html
2. Login dengan:
   - Email: admin@kampus.ac.id
   - Password: admin123
3. ✅ Harus redirect ke admin.html
4. ✅ Bisa akses semua fitur admin panel
```

### Test 2: Login sebagai Staff
```
1. Buka http://localhost/peminjaman/auth.html
2. Login dengan:
   - Email: staff@kampus.ac.id
   - Password: staff123
3. ✅ Harus redirect ke admin.html
4. ✅ Bisa approve/reject
5. ❌ Tidak bisa tambah/edit fasilitas (button disabled)
```

### Test 3: Login sebagai User
```
1. Buka http://localhost/peminjaman/auth.html
2. Login dengan:
   - Email: user@kampus.ac.id
   - Password: user123
3. ✅ Harus redirect ke index.html
4. ❌ Tidak bisa akses admin.html (redirect ke login)
```

### Test 4: Akses Admin Panel Tanpa Login
```
1. Pastikan belum login (clear localStorage)
2. Buka http://localhost/peminjaman/admin.html langsung
3. ✅ Harus muncul alert "Harus login"
4. ✅ Redirect ke auth.html
```

## 📁 File yang Diubah

### 1. `auth.html`
- Update API URL untuk support PHP backend
- Tambah logic redirect berdasarkan role
- Admin/Staff → admin.html
- User → index.html

### 2. `admin.html`
- Tambah proteksi akses di awal load
- Update API URL untuk support PHP backend
- Tambah validasi role (admin/staff)
- Redirect jika tidak punya akses

### 3. `assets/js/app.js`
- Update fungsi `protectAdmin()` untuk support staff
- Update display topbar untuk tampilkan role
- Support API user dari localStorage

## 🚀 Cara Menggunakan

### Untuk Admin:
1. Login dengan email admin
2. Otomatis masuk ke Admin Panel
3. Kelola semua peminjaman dan fasilitas

### Untuk Staff:
1. Login dengan email staff
2. Otomatis masuk ke Admin Panel
3. Kelola peminjaman (approve/reject)
4. Lihat laporan

### Untuk User Biasa:
1. Login dengan email user atau register baru
2. Masuk ke beranda
3. Ajukan peminjaman di menu Form Peminjaman
4. Cek status di menu Konfirmasi

## 🔧 Backend Integration

### Menggunakan Backend PHP:
```javascript
// Backend PHP otomatis terdeteksi di:
const API = 'http://localhost/peminjaman/api';

// Response format sama:
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@kampus.ac.id",
    "role": "admin"
  },
  "token": "eyJhbGciOi..."
}
```

### Menggunakan Backend Node.js:
```javascript
// Fallback otomatis ke:
const API = 'http://localhost:4000/api';
```

### Menggunakan localStorage (Offline):
```javascript
// Jika tidak ada backend, gunakan:
SPFK.users.login(email, password);
```

## ⚠️ Important Notes

1. **Password Default**: Ganti password default setelah instalasi
2. **JWT Token**: Berlaku 7 hari, setelah itu harus login ulang
3. **Session**: Token disimpan di localStorage
4. **Security**: Gunakan HTTPS di production
5. **Role**: Tidak bisa diubah via frontend, hanya via database

## 🐛 Troubleshooting

### Problem: Redirect loop
**Solution**: Clear localStorage dan login ulang
```javascript
localStorage.clear();
location.reload();
```

### Problem: Tidak bisa akses admin panel
**Solution**: 
1. Cek role di database (harus 'admin' atau 'staff')
2. Generate password hash baru
3. Clear localStorage dan login ulang

### Problem: Backend tidak terdeteksi
**Solution**:
1. Pastikan XAMPP Apache running
2. Pastikan database sudah diimport
3. Cek URL: `http://localhost/peminjaman/api/`

## 📞 Support

Jika ada masalah:
1. Cek console browser (F12) untuk error
2. Cek role di database: `SELECT * FROM users;`
3. Test backend: `http://localhost/peminjaman/api/`
4. Clear localStorage dan login ulang

---

**Update Date**: October 24, 2025  
**Version**: 1.1.0  
**Status**: ✅ Ready to Use
