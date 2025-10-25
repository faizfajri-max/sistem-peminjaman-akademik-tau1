# ⚡ QUICK FIX - Password & Users Management

## 🚨 Problem
- ❌ Login admin/staff gagal
- ❌ Password salah
- ❌ Tidak ada fitur kelola users

## ✅ Solution

### **Step 1: Update Database** (2 menit)

```
1. Buka: http://localhost/phpmyadmin
2. Klik database: peminjaman_db
3. Tab "SQL"
4. Copy-paste script di bawah:
```

```sql
-- Copy script ini:
UPDATE users SET password = '$2y$10$e0MYzXyjpJS7Pd6hUq.LCOCj4vJs0rg4wEhWzFLcNjC.6NKrTH1Eq' 
WHERE email IN ('admin@kampus.ac.id', 'staff@kampus.ac.id', 'user@kampus.ac.id');
```

```
5. Klik "Go"
6. ✅ Selesai!
```

### **Step 2: Login** (30 detik)

```
1. Buka: http://localhost/peminjaman/admin-login.html

2. Login:
   Email: admin@kampus.ac.id
   Password: admin123

3. ✅ Masuk ke Admin Panel
```

### **Step 3: Kelola Users** (1 menit)

```
1. Di Admin Panel, klik tab "Users"
2. ✅ Lihat semua users
3. ✅ Ubah role user (dropdown)
4. ✅ Hapus user (button merah)
5. ✅ Search & filter
```

---

## 🎯 Yang Baru

### **1. Password Fixed**
| Email | Password | Role |
|-------|----------|------|
| admin@kampus.ac.id | admin123 | Admin |
| staff@kampus.ac.id | admin123 | Staff |
| user@kampus.ac.id | admin123 | User |

### **2. Tab Users Baru** (Admin Only)
- Lihat semua users dengan pagination
- Edit role via dropdown (admin/staff/user)
- Hapus user dengan confirm
- Search by nama/email
- Filter by role

### **3. Data Peminjaman Lengkap**
- 4 Approved
- 3 Pending
- 3 Done
- 2 Rejected

---

## 📸 Preview

### **Tab Users:**
```
+----+------------------+--------------------------+--------+------------+--------+
| ID | Nama             | Email                    | Role   | Terdaftar  | Aksi   |
+----+------------------+--------------------------+--------+------------+--------+
| 1  | Administrator    | admin@kampus.ac.id       | [Admin▼] | 24 Okt 25 | Hapus  |
| 2  | Staff Akademik   | staff@kampus.ac.id       | [Staff▼] | 24 Okt 25 | Hapus  |
| 3  | User Demo        | user@kampus.ac.id        | [User▼]  | 24 Okt 25 | Hapus  |
| 4  | Budi Santoso     | budi@student.kampus.ac.id| [User▼]  | 24 Okt 25 | Hapus  |
+----+------------------+--------------------------+--------+------------+--------+
```

**Fitur:**
- Dropdown role = Auto-save + confirm
- Button Hapus = Confirm + warning
- Search box = Real-time filter
- Filter role = Dropdown admin/staff/user

---

## 🧪 Test Checklist

- [ ] Login admin berhasil dengan password `admin123`
- [ ] Tab "Users" muncul di admin panel
- [ ] Lihat list users dengan pagination
- [ ] Ubah role user → confirm → berhasil
- [ ] Search "budi" → filter bekerja
- [ ] Filter by role "admin" → hanya admin
- [ ] Coba hapus user → confirm → berhasil
- [ ] Coba ubah role sendiri → disabled (proteksi)
- [ ] Coba hapus diri sendiri → disabled (proteksi)

---

## ⚠️ Important Notes

### **Proteksi Admin:**
- ❌ **Tidak bisa ubah role sendiri** (dropdown disabled)
- ❌ **Tidak bisa hapus diri sendiri** (button disabled)
- ✅ Prevent admin accident

### **Cascade Delete:**
- Hapus user = hapus semua loans user
- Hapus user = hapus semua comments user
- Warning dialog sebelum delete

### **Only Admin:**
- Tab "Users" hanya untuk admin
- Staff tidak bisa kelola users
- User biasa tidak lihat admin panel

---

## 🐛 Troubleshooting

### **Login Gagal?**
```sql
-- Cek password hash di database
SELECT email, LEFT(password, 30) as pass_preview FROM users;

-- Jika berbeda, update lagi:
UPDATE users SET password = '$2y$10$e0MYzXyjpJS7Pd6hUq.LCOCj4vJs0rg4wEhWzFLcNjC.6NKrTH1Eq' 
WHERE email = 'admin@kampus.ac.id';
```

### **Tab Users Tidak Muncul?**
- Pastikan login sebagai **admin** (bukan staff)
- Lihat topbar: "Pengguna: Admin"
- Staff tidak punya akses tab Users

### **Update Role Gagal?**
- Cek console browser (F12)
- Error "401" → token expired, login ulang
- Error "403" → mencoba edit sendiri (blocked)

---

## 📞 Need Help?

**Check:**
1. XAMPP Apache & MySQL running?
2. Backend test: http://localhost/peminjaman/api/
3. Database test: http://localhost/phpmyadmin
4. Console errors: Browser F12 → Console

**Files:**
- Full docs: `UPDATE_FIX_PASSWORD.md`
- SQL update: `api/database-update.sql`
- Password tool: `api/generate-password.php`

---

## 🎉 Done!

✅ Password fixed  
✅ Login berhasil  
✅ Tab Users available  
✅ Role management works  
✅ Delete user works  
✅ Protection works  

**Enjoy! 🚀**
