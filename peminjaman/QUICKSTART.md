# 🚀 Quick Start - Admin Panel

## Cara Tercepat (5 detik)

1. **Double-click file `admin.html`** ✅
2. Selesai! Panel akan terbuka di browser

> 💡 Panel otomatis berjalan dalam **Mode Lokal** (tidak perlu backend)

---

## Apa yang Bisa Dilakukan?

### ✅ TERSEDIA (Mode Lokal)
- ✓ Lihat daftar pengajuan peminjaman
- ✓ Approve/Reject/Selesaikan pengajuan  
- ✓ Filter berdasarkan status, unit, jenis ruangan
- ✓ Lihat KPI (Total, Disetujui, Pending)
- ✓ Lihat laporan dengan filter
- ✓ Lihat daftar fasilitas

### ⚠️ BUTUH BACKEND
- ✗ Tambah/Edit/Hapus fasilitas
- ✗ Notifikasi email otomatis
- ✗ Role-based access (Admin/Staff/Viewer)
- ✗ Multi-user login

---

## Upgrade ke Mode Backend (Opsional)

Jika ingin fitur lengkap:

### Langkah 1: Install Node.js
Download & install dari: https://nodejs.org (pilih versi LTS)

### Langkah 2: Jalankan Server
Buka PowerShell dan ketik:

\`\`\`powershell
cd "c:\\Users\\Faiz Fajri\\Downloads\\Peminjaman Fasilitas\\server"
npm install
npm start
\`\`\`

### Langkah 3: Login
1. Buka `auth.html`
2. Login dengan:
   - **Admin**: admin@kampus.ac.id / admin123
   - **Staff**: staff@kampus.ac.id / staff123

### Langkah 4: Buka Admin Panel
Buka `admin.html` - akan otomatis detect backend dan unlock semua fitur!

---

## Troubleshooting

**Q: Halaman blank/putih?**
A: Pastikan internet aktif (untuk load Tailwind & Vue dari CDN)

**Q: "Backend API tidak tersedia"?**
A: Ini normal! Panel tetap berfungsi. Pesan ini hanya info bahwa backend tidak jalan.

**Q: Data tidak muncul?**
A: Buat pengajuan test dulu di halaman `borrow.html`

**Q: Masih error?**
A: Buka `test-admin.html` untuk diagnostic otomatis

---

## Demo Data

Buat data test cepat:
1. Buka `borrow.html`
2. Isi form peminjaman
3. Submit
4. Refresh `admin.html`

Atau jalankan di console browser (F12):
\`\`\`javascript
// Copy-paste script ini di console
SPFK.addLoan({
  id: 'LN_demo1',
  borrowerName: 'Budi Santoso',
  identity: '2021001',
  unit: 'Teknik Informatika',
  roomType: 'Kelas',
  facilityId: 'kelas-a',
  facilityName: 'Ruang Kelas A101',
  startDate: new Date('2025-10-28T08:00'),
  endDate: new Date('2025-10-28T10:00'),
  status: 'pending',
  notes: 'Kuliah Pemrograman Web',
  createdAt: new Date()
});
location.reload();
\`\`\`

---

**✨ Selamat Menggunakan Admin Panel!**
