# Akun Default Sistem Peminjaman

## ⚠️ PENTING: Reset Users Terlebih Dahulu!

**Jika Anda tidak bisa login dengan akun admin/staff:**

1. Buka: **http://localhost/peminjaman/reset-users.html**
2. Klik tombol **"🔄 Reset Users Sekarang"**
3. Tunggu hingga otomatis redirect ke halaman login
4. Login dengan akun di bawah ini

---

## Akun untuk Testing

### 1. Admin
- **Email**: `admin@admin.tau.ac.id`
- **Password**: `admin123`
- **Role**: `admin`
- **Akses**: Semua halaman termasuk Admin Panel

### 2. Staff
- **Email**: `staff@staff.tau.ac.id`
- **Password**: `staff123`
- **Role**: `staff`
- **Akses**: Semua halaman termasuk Admin Panel

### 3. User Biasa (Mahasiswa/Dosen)
Registrasi sendiri dengan email:
- `nama@student.tau.ac.id`
- `nama@admin.tau.ac.id`  
- `nama@staff.tau.ac.id`

**Role**: `user` (otomatis)
**Akses**: Semua halaman, tapi Admin Panel hanya bisa dilihat (tidak bisa diklik)

---

## Menu Admin Panel

Menu "Admin Panel" di sidebar:
- ✅ **HANYA MUNCUL** untuk user dengan role `admin` dan `staff`
- ❌ **DISEMBUNYIKAN** untuk user biasa (tidak terlihat sama sekali)
- 🔒 Proteksi ganda: Menu tersembunyi DAN halaman admin.html diproteksi

Jika user biasa mencoba akses langsung ke `admin.html`, akan ditolak dan dialihkan ke `index.html`.

---

## Cara Login Sebagai Admin/Staff

### Step by Step:

**1. Reset Users Dulu (Jika baru pertama kali atau lupa password)**
```
Buka: http://localhost/peminjaman/reset-users.html
Klik: "🔄 Reset Users Sekarang"
```

**2. Login**
```
Buka: http://localhost/peminjaman/login.html
Email: admin@admin.tau.ac.id
Password: admin123
Klik: "Masuk"
```

**3. Cek Menu Admin Panel**
```
- Buka halaman mana saja (index.html, borrow.html, dll)
- Lihat sidebar kiri
- Menu "Admin Panel" HARUS MUNCUL (jika login sebagai admin/staff)
- Menu "Admin Panel" TIDAK MUNCUL (jika login sebagai user biasa)
- Klik "Admin Panel" → Masuk ke halaman admin
```

---

## Troubleshooting

### ❌ Menu Admin Panel tidak muncul sama sekali
**Cek dulu:** Apakah Anda login sebagai admin/staff?

**Jika login sebagai USER BIASA** → Menu memang TIDAK MUNCUL (ini normal!)

**Jika login sebagai ADMIN/STAFF tapi menu tidak muncul:**
1. Logout dan login ulang
2. Buka Console (F12) → ketik: `SPFK.getAuth()` → Cek role
3. Jika role bukan 'admin' atau 'staff', reset users:
   - Buka: http://localhost/peminjaman/reset-users.html
   - Reset dan login ulang

### ❌ Menu Admin Panel muncul tapi redup/tidak bisa diklik
**Ini tidak akan terjadi lagi** - Menu sekarang disembunyikan total untuk user biasa

### ❌ Tidak bisa login dengan akun admin
**Solusi:**
1. Buka: http://localhost/peminjaman/reset-users.html
2. Reset users
3. Login lagi

---

## Reset Data (Jika Perlu)

### Reset Semua (Users + Data):
```javascript
// Browser Console (F12)
localStorage.clear();
location.reload();
```

### Reset Users Saja:
```
Buka: http://localhost/peminjaman/reset-users.html
Klik: "Reset Users"
```

---

## Validasi Email

Sistem hanya menerima email dengan domain:
- ✅ `@student.tau.ac.id`
- ✅ `@admin.tau.ac.id`
- ✅ `@staff.tau.ac.id`

Email lain akan **DITOLAK**!
