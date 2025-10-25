# SPFK Server (Express + SQLite)

Backend sederhana untuk Sistem Peminjaman Fasilitas Kampus.

## Fitur
- Auth JWT (admin, staff, viewer)
- Loans API (buat/list/filter, ubah status: approved/rejected/done)
- Facilities CRUD
- Reports summary
- **Comments API untuk dokumentasi pengembalian dengan upload foto**
- Notifikasi email via Nodemailer (opsional, log ke console jika SMTP tidak disetel)

## Menjalankan
1. Masuk ke folder `server` dan install dependensi:

```powershell
cd "c:\Users\Faiz Fajri\Downloads\Peminjaman Fasilitas\server"
npm install
```

2. Salin `.env.example` menjadi `.env` dan sesuaikan jika perlu.

3. Jalankan server:

```powershell
npm run start
```

Server tersedia di `http://localhost:4000`.

## Endpoint Utama
- POST `/api/auth/register` — buat pengguna (default role viewer)
- POST `/api/auth/login` — masuk, balikan `{ token, user }`
- GET `/api/auth/me` — informasi user saat ini
- GET `/api/loans` — list (filter: status, unit, from, to, roomType)
- POST `/api/loans` — buat pengajuan (butuh Bearer token)
- PATCH `/api/loans/:id/status` — ubah status (admin/staff)
- GET `/api/loans/:id` — detail + items
- GET `/api/facilities` — list fasilitas
- POST `/api/facilities` — tambah (admin)
- PUT `/api/facilities/:id` — ubah (admin)
- DELETE `/api/facilities/:id` — hapus (admin)
- GET `/api/reports/summary` — rekap status (admin/staff)
- **GET `/api/comments/:loanId` — ambil komentar untuk loan**
- **POST `/api/comments/:loanId` — tambah komentar + foto (auth required)**
- **PATCH `/api/comments/:loanId/mark-returned` — tandai loan selesai (admin/staff)**

## Akun Seed
- Admin: `admin@kampus.ac.id` / `admin123`
- Staff: `staff@kampus.ac.id` / `staff123`
- Viewer: `viewer@kampus.ac.id` / `viewer123`

## Integrasi Frontend
Set `CORS_ORIGIN` pada `.env` agar sesuai dengan origin frontend (contoh: `http://localhost:5500`). Frontend mengirim Authorization: `Bearer <token>`.
