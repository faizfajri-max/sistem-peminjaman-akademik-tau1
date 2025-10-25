# Troubleshooting Admin Panel

## Masalah: Admin Panel Tidak Bisa Dibuka

### Solusi Cepat

1. **Buka file test-admin.html terlebih dahulu**
   - Double-click file `test-admin.html` di folder proyek
   - Akan menampilkan hasil test semua komponen
   - Jika ada yang gagal, ikuti petunjuk di bawah

2. **Buka admin.html langsung**
   - Double-click file `admin.html`
   - Halaman akan terbuka di browser default Anda
   - **Mode Lokal**: Jika backend API tidak jalan, halaman akan otomatis menggunakan localStorage
   - **Mode Backend**: Jika server jalan di http://localhost:4000, akan terhubung ke API

### Mode Penggunaan

#### Mode Lokal (Tanpa Backend)
✅ **Keuntungan:**
- Bisa langsung dibuka tanpa install apapun
- Data tersimpan di browser (localStorage)
- Tab Pengajuan dan Laporan berfungsi penuh

⚠️ **Keterbatasan:**
- Tidak bisa tambah/edit/hapus fasilitas (data hardcoded)
- Tidak ada notifikasi email
- Tidak ada role management (semua user = admin)

**Cara Pakai:**
1. Double-click `admin.html`
2. Panel akan otomatis detect mode lokal
3. Langsung bisa lihat dan kelola pengajuan

#### Mode Backend (Dengan API)
✅ **Keuntungan:**
- Fitur lengkap: CRUD fasilitas, role management, email notifikasi
- Data tersimpan di database SQLite
- Multi-user dengan hak akses berbeda

**Cara Pakai:**
1. Install Node.js jika belum ada
2. Buka PowerShell di folder `server`
3. Jalankan:
   ```powershell
   cd server
   npm install
   npm start
   ```
4. Server akan jalan di http://localhost:4000
5. Buka `auth.html` untuk login
6. Buka `admin.html` - akan otomatis detect backend

**Akun Default:**
- Admin: `admin@kampus.ac.id` / `admin123`
- Staff: `staff@kampus.ac.id` / `staff123`
- Viewer: `viewer@kampus.ac.id` / `viewer123`

### Troubleshooting Spesifik

#### Problem: Halaman putih/blank
**Penyebab:** Browser memblokir script CDN atau file tidak ditemukan

**Solusi:**
1. Pastikan koneksi internet aktif (untuk CDN Tailwind & Vue)
2. Cek console browser (F12) untuk error
3. Pastikan file `assets/js/app.js` dan `assets/css/style.css` ada

#### Problem: "Backend API tidak tersedia"
**Ini bukan error!** Halaman akan tetap berfungsi dalam mode lokal.

**Jika ingin pakai backend:**
1. Install Node.js dari https://nodejs.org
2. Buka PowerShell di folder server
3. Jalankan `npm install` lalu `npm start`

#### Problem: Data tidak muncul
**Solusi:**
1. Buka `borrow.html` dan buat beberapa pengajuan test
2. Refresh halaman admin
3. Atau buka browser console (F12) dan jalankan:
   ```javascript
   // Tambah data test
   SPFK.addLoan({
     id: 'TEST001',
     borrowerName: 'Test User',
     identity: '12345',
     unit: 'Informatika',
     roomType: 'Kelas',
     facilityId: 'kelas-a',
     facilityName: 'Ruang Kelas A101',
     startDate: new Date(),
     endDate: new Date(),
     status: 'pending',
     notes: 'Test',
     createdAt: new Date()
   });
   location.reload();
   ```

#### Problem: Tab Fasilitas tidak bisa tambah/edit
Ini normal di **mode lokal**. Gunakan backend API untuk fitur CRUD fasilitas.

### Cara Menjalankan Backend (Opsional)

1. **Install Node.js**
   - Download dari https://nodejs.org
   - Pilih versi LTS
   - Install dengan setting default

2. **Setup Backend**
   ```powershell
   # Masuk ke folder server
   cd "c:\Users\Faiz Fajri\Downloads\Peminjaman Fasilitas\server"
   
   # Install dependensi
   npm install
   
   # Jalankan server
   npm start
   ```

3. **Verifikasi**
   - Buka browser: http://localhost:4000/api/health
   - Harus muncul: `{"ok":true}`

### Kontak

Jika masih ada masalah:
1. Buka file `test-admin.html` dan screenshot hasilnya
2. Buka console browser (F12) di tab admin.html dan screenshot error yang muncul
3. Share screenshot tersebut untuk diagnostic lebih lanjut
