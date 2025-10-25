# 📋 Admin Panel - Form Submissions Feature

## Fitur Baru di Admin Panel

Admin sekarang bisa melihat semua hasil form pengisian dari halaman `borrow.html` dengan fitur lengkap:

### ✨ Fitur Utama

#### 1. **Tab "Form Submissions" (📋 Bookings)**
- Menampilkan semua pengajuan peminjaman dari form publik
- Filter berdasarkan status (Pending, Approved, Rejected, Done)
- Tabel lengkap dengan informasi:
  - Kode Booking (BK_xxxxx)
  - Nama Peminjam & NIM/NIP
  - Unit/Prodi
  - Fasilitas yang dipinjam
  - Waktu peminjaman
  - Status

#### 2. **Detail Booking Modal**
Klik tombol "👁️ Detail" untuk melihat informasi lengkap:

**📝 Informasi Peminjam:**
- Nama lengkap
- NIM/NIP
- Unit/Prodi

**🏢 Fasilitas yang Dipinjam:**
- Nama fasilitas
- Jenis ruangan

**📅 Waktu Peminjaman:**
- Tanggal
- Jam mulai - jam selesai

**🛠️ Fasilitas Tambahan:**
- Daftar peralatan yang diminta (HDMI, Infocus, Kursi, dll)
- Jumlah masing-masing item

**📝 Catatan:**
- Notes dari peminjam

**📄 Dokumen Upload:**
- Nama file yang diupload
- Tombol download untuk melihat file

**⏰ Timestamps:**
- Waktu dibuat
- Waktu terakhir diupdate

#### 3. **Manajemen Status**
Admin dapat mengubah status booking:
- **✅ Setuju** - Approve peminjaman
- **❌ Tolak** - Reject peminjaman
- **✔️ Selesai** - Tandai sebagai done

#### 4. **File Upload & Download**
- User dapat upload dokumen saat mengisi form (max 5MB)
- Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
- Admin dapat download file dari detail modal
- File disimpan di folder `api/uploads/`

### 🔧 Setup Database

Jalankan setup tool untuk membuat tabel bookings:
```
http://localhost/peminjaman/api/setup-bookings.php
```

Tool ini akan:
- Membuat tabel `bookings` dan `booking_facilities`
- Insert sample data
- Verifikasi struktur database

### 📊 Akses Fitur

**URL Admin Panel:**
```
http://localhost/peminjaman/admin.html
```

**Login sebagai Admin:**
- Email: `admin@kampus.ac.id`
- Password: `admin123`

**Atau Staff:**
- Email: `staff@kampus.ac.id`
- Password: `admin123`

### 🎯 Flow Penggunaan

#### Untuk User (Peminjam):
1. Buka `http://localhost/peminjaman/borrow.html`
2. Isi form peminjaman lengkap
3. Upload dokumen (opsional)
4. Submit form
5. Data tersimpan ke database + localStorage
6. Redirect ke halaman konfirmasi

#### Untuk Admin:
1. Login ke admin panel
2. Klik tab "📋 Form Submissions"
3. Lihat daftar semua booking
4. Klik "👁️ Detail" untuk melihat info lengkap
5. Download file jika ada
6. Approve/Reject/Tandai selesai

### 🗄️ Database Schema

**Tabel: `bookings`**
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- booking_id (VARCHAR 50, UNIQUE) - Format: BK_xxxxx
- borrower_name (VARCHAR 255)
- identity (VARCHAR 100) - NIM/NIP
- unit (VARCHAR 255) - Prodi/Unit
- facility_id (INT)
- facility_name (VARCHAR 255)
- room_type (VARCHAR 100)
- start_date (DATETIME)
- end_date (DATETIME)
- notes (TEXT)
- document_name (VARCHAR 255) - Uploaded file name
- status (ENUM: pending, approved, rejected, done)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

**Tabel: `booking_facilities`**
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- booking_id (INT, FOREIGN KEY)
- item (VARCHAR 100) - Nama item (HDMI, Mic, dll)
- quantity (INT) - Jumlah
- created_at (TIMESTAMP)
```

### 🔌 API Endpoints

**GET /api/bookings**
- List all bookings
- Query params: `status` (optional)

**GET /api/bookings/:bookingId**
- Get booking detail by booking_id (BK_xxxxx format)
- Includes facilities array

**POST /api/bookings**
- Create new booking from form
- Body: bookingId, borrowerName, identity, unit, facilityId, etc.

**PATCH /api/bookings/:bookingId/status**
- Update booking status
- Body: { status: 'approved' | 'rejected' | 'done' }

**POST /api/upload.php**
- Upload file
- FormData with 'document' field
- Returns: { success, filename, url }

### 📁 File Structure

```
api/
  ├── routes/
  │   └── bookings.php          # Bookings API routes
  ├── upload.php                 # File upload handler
  ├── uploads/                   # Uploaded files directory
  ├── setup-bookings.php         # Setup tool
  └── create-bookings-table.sql  # Database schema

borrow.html                      # Form peminjaman (with upload)
admin.html                       # Admin panel (with bookings tab)
confirmation.html                # Confirmation page (loads from DB)
```

### 🎨 Features Highlights

✅ **Real-time Database Sync** - Data langsung tersimpan ke MySQL
✅ **File Upload** - Support dokumen proposal/surat izin
✅ **Admin Review** - Admin bisa review lengkap sebelum approve
✅ **Status Management** - Track status dari pending → approved → done
✅ **Responsive UI** - Modal detail yang user-friendly
✅ **Error Handling** - Graceful fallback ke localStorage jika API error
✅ **Security** - File validation (type & size)
✅ **Multi-format Support** - PDF, Word, Excel, Images

### 🐛 Troubleshooting

**Error: "Table bookings doesn't exist"**
→ Jalankan `http://localhost/peminjaman/api/setup-bookings.php`

**Error: "Access denied"**
→ Login sebagai Admin atau Staff

**File upload gagal**
→ Cek folder `api/uploads/` memiliki permission 755

**Data tidak muncul**
→ Cek console browser (F12) untuk error messages

### 🚀 Next Steps

1. Jalankan setup-bookings.php untuk create tables
2. Login ke admin panel
3. Test submit form dari borrow.html
4. Cek di tab "Form Submissions"
5. Review detail dan approve

---

**Made with ❤️ for Sistem Peminjaman Fasilitas Kampus**
