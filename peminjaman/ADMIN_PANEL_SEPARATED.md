# Update: Admin Panel Terpisah

## 📝 Perubahan Terbaru

Admin Panel sekarang **sepenuhnya terpisah** dari sistem user biasa dengan halaman login khusus.

## 🎯 Yang Berubah

### 1. **Admin Panel Disembunyikan dari Menu**

Link "Admin Panel" di sidebar **hanya muncul** untuk user yang sudah login sebagai Admin/Staff:

- ❌ User biasa: **TIDAK** melihat link Admin Panel
- ❌ User belum login: **TIDAK** melihat link Admin Panel  
- ✅ Admin login: **MELIHAT** link Admin Panel
- ✅ Staff login: **MELIHAT** link Admin Panel

### 2. **Halaman Login Admin Terpisah**

Dibuat halaman baru: **`admin-login.html`**

- 🎨 Desain khusus untuk admin
- 🔐 Validasi role saat login
- ⚡ Auto-redirect jika sudah login
- 🚫 Reject user biasa yang coba akses

### 3. **Alur Login Terpisah**

#### Login User Biasa (`auth.html`)
```
User buka auth.html
    ↓
Login (admin/staff/user)
    ↓
Semua → Redirect ke index.html
    ↓
Link Admin Panel hanya muncul untuk admin/staff
```

#### Login Admin/Staff (`admin-login.html`)
```
Admin buka admin-login.html
    ↓
Login dengan email admin/staff
    ↓
Validasi role (harus admin/staff)
    ↓
Redirect langsung ke admin.html
```

## 🚀 Cara Akses Admin Panel

### Untuk Admin/Staff:

**Opsi 1: Via URL Langsung**
```
1. Buka: http://localhost/peminjaman/admin-login.html
2. Login dengan:
   - Admin: admin@kampus.ac.id / admin123
   - Staff: staff@kampus.ac.id / staff123
3. Otomatis masuk ke Admin Panel
```

**Opsi 2: Via Menu (Setelah Login)**
```
1. Login via auth.html
2. Link "Admin Panel" muncul di sidebar
3. Klik untuk akses
```

### Untuk User Biasa:

```
❌ Tidak bisa akses admin-login.html
❌ Tidak melihat link Admin Panel di menu
✅ Hanya bisa menggunakan fitur user biasa
```

## 📋 URL Struktur

| URL | Akses | Fungsi |
|-----|-------|--------|
| `/auth.html` | Public | Login user biasa & register |
| `/admin-login.html` | Public | Login khusus admin/staff |
| `/admin.html` | Admin/Staff Only | Admin Panel dashboard |
| `/index.html` | Public | Beranda untuk semua |

## 🔐 Keamanan

### Proteksi Berlapis:

1. **Hidden Menu Link**
   - Link Admin Panel disembunyikan via JavaScript
   - Hanya tampil setelah validasi role

2. **Login Validation**
   - `admin-login.html` validate role saat login
   - Reject user biasa dengan alert

3. **Page Protection**
   - `admin.html` cek role saat page load
   - Auto-redirect jika tidak authorized

4. **Session Check**
   - Validasi token JWT dari backend
   - Fallback ke localStorage auth

## 🧪 Testing

### Test 1: User Biasa Tidak Lihat Menu
```
1. Logout semua akun
2. Buka index.html atau auth.html
3. ✅ Link "Admin Panel" TIDAK muncul di sidebar
```

### Test 2: Admin Login via admin-login.html
```
1. Buka: http://localhost/peminjaman/admin-login.html
2. Login: admin@kampus.ac.id / admin123
3. ✅ Redirect langsung ke admin.html
```

### Test 3: User Biasa Coba Login via admin-login.html
```
1. Buka: admin-login.html
2. Login: user@kampus.ac.id / user123
3. ✅ Muncul alert "Akses ditolak"
4. ✅ Tidak bisa masuk
```

### Test 4: Admin Login via auth.html (Login Biasa)
```
1. Buka: auth.html
2. Login: admin@kampus.ac.id / admin123
3. ✅ Redirect ke index.html
4. ✅ Link "Admin Panel" muncul di sidebar
5. ✅ Klik bisa akses admin.html
```

### Test 5: Akses Langsung admin.html Tanpa Login
```
1. Clear localStorage (belum login)
2. Buka: http://localhost/peminjaman/admin.html
3. ✅ Alert "Harus login"
4. ✅ Redirect ke admin-login.html
```

### Test 6: Admin Logout dari Admin Panel
```
1. Login sebagai admin
2. Buka admin.html
3. Klik tombol "Keluar"
4. ✅ Redirect ke admin-login.html
5. ✅ Link Admin Panel hilang dari sidebar
```

## 📁 File Baru & Modifikasi

### File Baru:
- ✨ `admin-login.html` - Halaman login khusus admin/staff

### File Dimodifikasi:
- 📝 `assets/js/app.js` - Tambah logic hide/show menu admin
- 📝 `admin.html` - Update redirect & logout handler
- 📝 `auth.html` - Redirect semua ke index.html

## 🎨 Tampilan admin-login.html

**Fitur:**
- 🎨 Desain modern dengan gradient background
- 📱 Responsive design
- 🔔 Status backend indicator (PHP/Node.js/Offline)
- ⚡ Auto-redirect jika sudah login
- 🚫 Validasi role built-in
- 💡 Info akun default

## 🔄 Alur Lengkap

### Scenario 1: Admin Mau Akses Panel
```
Admin → admin-login.html → Login → admin.html
                                    ↓
                            (Kelola sistem)
                                    ↓
                            Logout → admin-login.html
```

### Scenario 2: User Biasa
```
User → auth.html → Login → index.html
                              ↓
                    (Tidak lihat Admin Panel)
                              ↓
                    (Gunakan fitur user)
```

### Scenario 3: Admin Login via Auth Biasa
```
Admin → auth.html → Login → index.html
                               ↓
                    (Lihat link Admin Panel di menu)
                               ↓
                    Klik → admin.html
```

## ⚙️ Konfigurasi Backend

Backend otomatis terdeteksi dengan prioritas:

1. **PHP Backend** (`http://localhost/peminjaman/api`)
2. **Node.js Backend** (`http://localhost:4000/api`)
3. **localStorage** (Mode offline)

Tidak perlu konfigurasi tambahan!

## 💡 Keuntungan

### Untuk User:
- ✅ Interface lebih bersih (tidak ada menu admin)
- ✅ Tidak bingung dengan fitur yang tidak bisa diakses
- ✅ Fokus pada fitur peminjaman

### Untuk Admin:
- ✅ Login terpisah lebih aman
- ✅ Akses langsung ke panel admin
- ✅ Interface khusus untuk admin
- ✅ Clear separation of concerns

### Untuk Security:
- ✅ Double validation (login + page load)
- ✅ Hidden menu untuk unauthorized
- ✅ Explicit admin-only page
- ✅ Proper session management

## 🐛 Troubleshooting

### Problem: Link Admin Panel tidak muncul setelah login
**Solution:**
```javascript
// Refresh page setelah login
location.reload();

// Atau clear cache dan login ulang
localStorage.clear();
location.reload();
```

### Problem: Redirect loop di admin panel
**Solution:**
```javascript
// Clear localStorage
localStorage.removeItem('spfk_api_token');
localStorage.removeItem('spfk_api_user');
SPFK.logout();

// Login ulang via admin-login.html
```

### Problem: Backend tidak terdeteksi
**Solution:**
1. Cek XAMPP Apache running
2. Test: `http://localhost/peminjaman/api/`
3. Pastikan database sudah diimport

## 📞 Support

Jika ada masalah:
1. Buka Console browser (F12)
2. Cek error messages
3. Verify role di database: `SELECT * FROM users;`
4. Clear localStorage dan login ulang

---

**Update Date**: October 24, 2025  
**Version**: 1.2.0  
**Status**: ✅ Production Ready

## 🎉 Summary

Sekarang sistem memiliki **pemisahan yang jelas** antara:
- 👥 **User Interface** - Untuk mahasiswa/umum
- 🔐 **Admin Interface** - Untuk admin/staff saja

Link Admin Panel **otomatis tersembunyi** dari user biasa dan **hanya muncul** saat admin/staff login!
