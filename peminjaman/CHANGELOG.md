# Changelog - Sistem Peminjaman Fasilitas Kampus

## [2025-10-24] - Version 1.3.0 - Password Fix & Users Management

### 🔧 Fixed
- ✅ **Password Authentication**: Fixed bcrypt hash untuk semua default users
- ✅ Login admin/staff sekarang berfungsi dengan password: `admin123`
- ✅ Hash password valid: `$2y$10$e0MYzXyjpJS7Pd6hUq.LCOCj4vJs0rg4wEhWzFLcNjC.6NKrTH1Eq`

### ✨ Added
- ✅ **Users Management Tab**: Admin dapat kelola users (view/edit role/delete)
- ✅ **3 API Endpoints Baru**:
  - `GET /api/users` - List users dengan pagination & filter
  - `PUT /api/users/:id/role` - Update user role (admin only)
  - `DELETE /api/users/:id` - Delete user dengan cascade
- ✅ **Sample Data**: 12 peminjaman lengkap (4 approved, 3 pending, 3 done, 2 rejected)
- ✅ **6 Users**: admin, staff, user + 3 mahasiswa sample
- ✅ **Helper Tool**: `generate-password.php` untuk generate hash password
- ✅ **Documentation**: 3 file baru (UPDATE_FIX_PASSWORD.md, QUICK_FIX.md, detail lengkap)

### 🔐 Security
- ✅ Role-based access control (admin/staff/user)
- ✅ Self-protection: tidak bisa edit/delete diri sendiri
- ✅ Cascade delete: hapus user = hapus loans & comments terkait
- ✅ Validation: JWT token, role check, user existence

### 📁 Files Changed
- `api/database.sql` - Updated password hash + sample users
- `api/routes/auth.php` - Added 3 new methods untuk users management
- `api/index.php` - Added users routes
- `admin.html` - Added Users tab dengan UI lengkap

### 📁 Files Added
- `api/database-update.sql` - SQL untuk update existing database
- `api/generate-password.php` - Password hash generator tool
- `UPDATE_FIX_PASSWORD.md` - Comprehensive documentation
- `QUICK_FIX.md` - Quick start guide (2 min setup)

### 🧪 Testing
✅ All tests passing:
- Login authentication
- Users list & pagination
- Role update with validation
- User delete with cascade
- Self-protection mechanisms
- Search & filter functionality

### 📊 Statistics
- ~1,200 lines of code/documentation added
- 3 new API endpoints
- 6 sample users
- 12 sample loans

---

## [2025-10-24] - Perubahan Validasi H-7

### Changed
- ✅ **Validasi Peminjaman**: Diubah dari **H-3** menjadi **H-7** (minimal 7 hari sebelum acara)
  - **Frontend** (`borrow.html`): Update validasi dan error message
  - **Backend** (`server/src/routes/loans.js`): Tambah validasi H-7 di API endpoint
  
### Alasan Perubahan
Memberikan waktu lebih banyak untuk persiapan dan menghindari bentrok jadwal yang mendadak.

### Impact
- Pengguna sekarang harus mengajukan peminjaman **minimal 7 hari sebelumnya**
- Error message: "Peminjaman minimal H-7 (7 hari) dari hari ini."
- Validasi berlaku di frontend dan backend untuk konsistensi

---

## [2025-01-10] - Fitur Return Confirmation

### Added
- ✅ Chat-style timeline untuk dokumentasi pengembalian
- ✅ Upload foto bukti pengembalian (max 5MB)
- ✅ Comments API endpoints (GET/POST/PATCH)
- ✅ Admin/staff dapat mark as returned
- ✅ Dual-mode support (localStorage + API)

### Files
- `confirmation.html` - Vue component dengan timeline
- `server/src/routes/comments.js` - API routes
- `server/src/lib/db.js` - Comments table
- Documentation: `RETURN_CONFIRMATION.md`, `DEMO_RETURN.md`

---

## [Initial Release] - Complete System

### Features
- ✅ 7 pages: Home, Facilities, Schedule, Borrow Form, Confirmation, Admin Panel, Auth
- ✅ Vue 3 + Tailwind CSS frontend
- ✅ Node.js + Express + SQLite backend
- ✅ JWT authentication (admin/staff/viewer roles)
- ✅ Email notifications
- ✅ Dual-mode operation (local + API)
- ✅ Responsive design with campus theme

### Documentation
- `QUICKSTART.md` - Quick start guide
- `TROUBLESHOOTING.md` - Troubleshooting guide
- `server/README.md` - Backend documentation
