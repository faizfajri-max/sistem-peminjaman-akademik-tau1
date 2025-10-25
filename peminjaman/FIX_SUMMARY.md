# 🔧 Perbaikan Bug - Document Upload & Status Update

## Tanggal: 24 Oktober 2025

---

## ❌ Masalah yang Dilaporkan

### 1. **Error Update Status**
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'peminjaman_db.bookings' doesn't exist
```

### 2. **Dokumen Tidak Muncul**
Pesan "Tidak ada dokumen yang diupload" muncul meskipun file sudah diupload.

---

## ✅ Solusi yang Diterapkan

### 1. **Membuat Tabel Database** 📊

**File:** `api/quick-setup.php` (BARU)

Tabel yang dibuat:
- `bookings` - Tabel utama untuk menyimpan data peminjaman
- `booking_facilities` - Tabel junction untuk relasi many-to-many

**Cara Menjalankan:**
```powershell
C:\xamppp\php\php.exe api/quick-setup.php
```

**Struktur Tabel `bookings`:**
```sql
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) UNIQUE NOT NULL,
    borrower_name VARCHAR(255) NOT NULL,
    identity VARCHAR(100) NOT NULL,
    unit VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    facility_name VARCHAR(255) NOT NULL,
    facility_id INT,
    event_name VARCHAR(255) NOT NULL,
    participant_count INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    purpose TEXT NOT NULL,
    document_name VARCHAR(255) DEFAULT NULL,  -- 👈 FIELD INI UNTUK DOKUMEN
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

---

### 2. **Perbaikan Document Display** 📄

#### A. **Perbaikan di `admin.html`**

**Masalah:** `loadBookingsLocal()` selalu set `document_name: ''` (kosong)

**Sebelum:**
```javascript
function loadBookingsLocal() {
  bookings.value = data.map(b => ({
    ...
    document_name: '',  // ❌ SELALU KOSONG!
    ...
  }));
}
```

**Sesudah:**
```javascript
function loadBookingsLocal() {
  bookings.value = data.map(b => ({
    ...
    document_name: b.documentName || b.document_name || '',  // ✅ MAPPING DARI localStorage
    identity: b.identity || '',
    start_time: b.startTime || '',
    end_time: b.endTime || '',
    ...
  }));
}
```

**Perubahan Kondisi v-if:**
```html
<!-- Sebelum -->
<div v-if="selectedBooking.document_name">

<!-- Sesudah -->
<div v-if="selectedBooking.document_name && selectedBooking.document_name.trim() !== ''">
```
*Ini menangani kasus di mana `document_name` adalah string kosong, bukan null.*

**Debugging Console Log:**
```javascript
async function viewBookingDetail(booking) {
  if (apiOk.value) {
    try {
      const response = await fetch(API + '/bookings/' + booking.booking_id);
      const data = await response.json();
      
      console.log('API Response:', data);  // ✅ TAMBAHAN
      console.log('Document name from API:', data.loan?.document_name);  // ✅ TAMBAHAN
      
      if (data.success && data.loan) {
        selectedBooking.value = data.loan;
      }
      
      console.log('Selected booking:', selectedBooking.value);  // ✅ TAMBAHAN
      console.log('Document name:', selectedBooking.value?.document_name);  // ✅ TAMBAHAN
    }
  }
}
```

---

#### B. **Perbaikan di `borrow.html`**

**Masalah:** Object `booking` yang disimpan ke localStorage tidak punya field `documentName`

**Sebelum:**
```javascript
const booking = {
  id: code,
  facilityId,
  facilityName: fac?.name || facilityId,
  requesterName: borrowerName,
  requesterEmail: '',
  unit,
  purpose: data.get('notes') || '',
  startDate: start,
  endDate: end,
  status: 'pending',
  createdBy: 'form-online',
  createdAt: new Date()
  // ❌ TIDAK ADA documentName!
};
```

**Sesudah:**
```javascript
const booking = {
  id: code,
  facilityId,
  facilityName: fac?.name || facilityId,
  requesterName: borrowerName,
  requesterEmail: '',
  identity,  // ✅ TAMBAHAN
  unit,
  purpose: data.get('notes') || '',
  startDate: start,
  endDate: end,
  startTime: data.get('startTime') || '',  // ✅ TAMBAHAN
  endTime: data.get('endTime') || '',  // ✅ TAMBAHAN
  documentName: uploadedFileName || '',  // ✅ TAMBAHAN - INI YANG PENTING!
  status: 'pending',
  createdBy: 'form-online',
  createdAt: new Date()
};
```

---

### 3. **Status Badge Responsif** 🎨

**File:** `admin.html`

#### Custom Tailwind Animations:
```javascript
tailwind.config = { 
  theme: { 
    extend: { 
      animation: {
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'shake': 'shake 0.5s ease-in-out infinite'
      },
      keyframes: {
        shake: {
          '0%, 100%': { transform: 'translateX(0)' },
          '10%, 30%, 50%, 70%, 90%': { transform: 'translateX(-2px)' },
          '20%, 40%, 60%, 80%': { transform: 'translateX(2px)' }
        }
      }
    }
  }
};
```

#### Enhanced Status Badge:
```html
<span :class="[
  'inline-flex items-center gap-2 px-4 py-2 rounded-full font-semibold text-sm 
   transition-all duration-300 transform hover:scale-105 hover:shadow-lg',
  selectedBooking.status === 'approved' ? 'bg-gradient-to-r from-green-400 to-green-600 text-white animate-pulse-slow' :
  selectedBooking.status === 'pending' ? 'bg-gradient-to-r from-yellow-400 to-orange-500 text-white' :
  selectedBooking.status === 'done' ? 'bg-gradient-to-r from-blue-400 to-blue-600 text-white' :
  'bg-gradient-to-r from-red-400 to-red-600 text-white animate-shake'
]">
  <span :class="[
    'w-2 h-2 rounded-full',
    selectedBooking.status === 'approved' ? 'bg-green-200 animate-ping' :
    selectedBooking.status === 'pending' ? 'bg-yellow-200 animate-pulse' :
    selectedBooking.status === 'done' ? 'bg-blue-200' :
    'bg-red-200 animate-ping'
  ]"></span>
  {{ selectedBooking.status === 'approved' ? 'Disetujui' : 
     selectedBooking.status === 'pending' ? 'Menunggu' : 
     selectedBooking.status === 'done' ? 'Selesai' : 
     selectedBooking.status === 'rejected' ? 'Ditolak' : selectedBooking.status }}
</span>
```

**Fitur:**
- ✅ Gradient warna sesuai status
- ✅ Hover effect: scale up + shadow
- ✅ Animated dot indicator (ping/pulse)
- ✅ Shake animation untuk status rejected
- ✅ Teks dalam Bahasa Indonesia

---

## 📋 Cara Testing

### 1. **Setup Database**
```powershell
cd C:\xamppp\htdocs\peminjaman
C:\xamppp\php\php.exe api/quick-setup.php
```

Output yang diharapkan:
```
✅ Tabel bookings berhasil dibuat!
✅ Tabel booking_facilities berhasil dibuat!
📊 Database siap! Jumlah booking: 0
```

---

### 2. **Test Upload Dokumen**

#### A. **Buat Peminjaman Baru**
1. Buka http://localhost/peminjaman/borrow.html
2. Isi semua field yang required
3. **Upload dokumen** (PDF, Word, Excel, atau gambar - max 5MB)
4. Submit form
5. Lihat pesan "✅ Upload sukses: namafile.pdf"
6. Redirect ke halaman konfirmasi

#### B. **Cek di Admin Panel**
1. Buka http://localhost/peminjaman/admin.html
2. Login dengan:
   - Email: `admin@kampus.ac.id`
   - Password: `admin123`
3. Tab "Form Submissions" akan terbuka otomatis
4. Klik **Detail** pada booking yang baru dibuat
5. **Buka Developer Console (F12)**
6. Perhatikan output console log:
   ```javascript
   API Response: { success: true, loan: {...} }
   Document name from API: doc_67398abc_1729756800.pdf
   Selected booking: { booking_id: 'BK_xxx', document_name: 'doc_...pdf', ... }
   Document name: doc_67398abc_1729756800.pdf
   ```
7. **Section dokumen harus muncul** dengan:
   - Ikon 📄
   - Gradient purple-pink background
   - Nama file ditampilkan
   - 2 tombol: "Download Dokumen" dan "View"

#### C. **Test Status Badge**
1. Masih di modal Detail
2. Lihat badge status di atas informasi peminjam
3. **Hover** badge → harus scale up + shadow
4. Badge **pending** → kuning-orange dengan dot beranimasi pulse
5. Update status ke **approved**
6. Badge berubah → hijau dengan animate-pulse-slow dan dot ping

---

### 3. **Test Error Handling**

#### A. **Tanpa Upload Dokumen**
1. Buat peminjaman TANPA upload file
2. Submit form
3. Di admin panel, klik Detail
4. Harus muncul: **"Tidak ada dokumen yang diupload"**
5. Section dokumen purple TIDAK muncul

#### B. **Dengan localStorage (Offline)**
1. Matikan Apache (stop XAMPP)
2. Buka http://localhost/peminjaman/borrow.html (dari cache browser)
3. Isi form + upload dokumen
4. Submit → data tersimpan ke localStorage
5. Buka admin panel
6. Data muncul dari localStorage dengan document_name yang benar

---

## 🔍 Debugging Tips

### Console Log Output yang Benar:
```javascript
// Saat klik Detail di admin panel:
API Response: {
  success: true,
  loan: {
    booking_id: "BK_it6de9d6i",
    borrower_name: "John Doe",
    document_name: "doc_67398abc_1729756800.pdf",  // ✅ ADA NILAINYA
    ...
  }
}
Document name from API: doc_67398abc_1729756800.pdf
Selected booking: { ..., document_name: "doc_67398abc_1729756800.pdf" }
Document name: doc_67398abc_1729756800.pdf
```

### Cek Database Langsung:
```sql
SELECT booking_id, borrower_name, document_name, status 
FROM bookings 
ORDER BY created_at DESC 
LIMIT 5;
```

### Cek File Upload:
```powershell
dir C:\xamppp\htdocs\peminjaman\api\uploads
```
Harus ada file dengan format: `doc_[uniqid]_[timestamp].[ext]`

---

## 📁 File yang Dimodifikasi

| File | Perubahan | Status |
|------|-----------|--------|
| `api/quick-setup.php` | **BARU** - Script setup database | ✅ Dibuat |
| `admin.html` | - Tambah console.log debugging<br>- Fix loadBookingsLocal() mapping<br>- Fix v-if condition untuk dokumen<br>- Enhanced status badge dengan animasi<br>- Tambah Tailwind custom animations | ✅ Diperbarui |
| `borrow.html` | - Tambah documentName ke object booking<br>- Tambah identity, startTime, endTime | ✅ Diperbarui |

---

## ⚠️ Catatan Penting

1. **Tabel harus dibuat dulu** - Jalankan `quick-setup.php` sebelum testing
2. **Field mapping localStorage** - Perhatikan camelCase vs snake_case:
   - localStorage: `documentName` (camelCase)
   - Database: `document_name` (snake_case)
   - Admin panel harus handle kedua format
3. **File upload** - Max 5MB, formats: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
4. **Console debugging** - Selalu buka Developer Console saat testing untuk lihat nilai document_name

---

## 🚀 Next Steps

Jika masih ada masalah:
1. Buka Developer Console (F12)
2. Lihat output console log
3. Cek nilai `document_name` di setiap tahap
4. Pastikan file ter-upload ke `api/uploads/`
5. Query database untuk cek nilai document_name

---

**Status Perbaikan:** ✅ **SELESAI**
- Database: ✅ Setup
- Document display: ✅ Fixed
- Status badge: ✅ Enhanced
- Debugging: ✅ Added

**Testing Required:** User perlu test upload dokumen baru untuk verifikasi fix.
