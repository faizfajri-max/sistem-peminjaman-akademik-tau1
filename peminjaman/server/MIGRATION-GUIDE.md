/3*6# Database Migration Guide

## Perubahan Database untuk Fitur Pemisahan Ruangan & Alat

### Kolom Baru di Tabel `loans`:

1. **`borrowType`** (TEXT, DEFAULT 'room')
   - Nilai: `'room'` atau `'equipment'`
   - Menandakan jenis peminjaman (ruangan atau alat)

2. **`quantity`** (INTEGER, DEFAULT 1)
   - Jumlah unit yang dipinjam (untuk equipment)
   - Untuk ruangan, selalu bernilai 1

3. **`roomType`** (TEXT, NULLABLE)
   - Sekarang bersifat opsional (tidak wajib untuk equipment)

---

## Cara Migrasi Database

### Opsi 1: Menggunakan SQL Script (RECOMMENDED)

1. Buka SQLite database (`spfk.db`) menggunakan DB Browser atau sqlite3 CLI
2. Jalankan perintah SQL berikut:

```sql
-- Add borrowType column
ALTER TABLE loans ADD COLUMN borrowType TEXT DEFAULT 'room';

-- Add quantity column  
ALTER TABLE loans ADD COLUMN quantity INTEGER DEFAULT 1;

-- Update existing records
UPDATE loans SET borrowType = 'room' WHERE borrowType IS NULL;
UPDATE loans SET quantity = 1 WHERE quantity IS NULL;
```

3. Verifikasi dengan:
```sql
PRAGMA table_info(loans);
```

### Opsi 2: Database Baru (Hapus database lama)

Jika Anda tidak memiliki data penting:

1. Hapus file `spfk.db` dan `spfk.db-wal` (jika ada)
2. Restart server - database baru akan dibuat otomatis dengan struktur terbaru

```bash
# Windows PowerShell
cd c:\xamppp\htdocs\peminjaman\server
Remove-Item spfk.db -ErrorAction SilentlyContinue
Remove-Item spfk.db-wal -ErrorAction SilentlyContinue
npm run dev
```

### Opsi 3: Menggunakan DB Browser for SQLite (GUI)

1. Download: https://sqlitebrowser.org/
2. Buka file `spfk.db`
3. Klik tab "Execute SQL"
4. Copy-paste isi file `migrate.sql`
5. Klik "Execute" (▶️)
6. Klik "Write Changes" untuk save

---

## Verifikasi Migrasi Berhasil

Setelah migrasi, struktur tabel `loans` harus seperti ini:

```
Column Name    | Type    | Not Null | Default
---------------|---------|----------|--------
id             | TEXT    | ✓        | -
borrowerName   | TEXT    | ✓        | -
identity       | TEXT    | ✓        | -
unit           | TEXT    | ✓        | -
borrowType     | TEXT    | ✗        | 'room'
roomType       | TEXT    | ✗        | -
facilityId     | TEXT    | ✓        | -
quantity       | INTEGER | ✗        | 1
startDate      | TEXT    | ✓        | -
endDate        | TEXT    | ✓        | -
notes          | TEXT    | ✗        | -
status         | TEXT    | ✓        | -
createdAt      | TEXT    | ✓        | -
updatedAt      | TEXT    | ✗        | -
```

---

## Testing

Setelah migrasi, test dengan:

1. Buka: http://localhost/peminjaman/borrow.html
2. Pilih "Jenis Peminjaman": **Ruangan**
   - Isi form dan submit
   - Cek database: `borrowType = 'room'`, `quantity = 1`
   
3. Pilih "Jenis Peminjaman": **Peralatan/Alat**
   - Pilih alat (misal: Kamera DSLR)
   - Set quantity: 1
   - Submit
   - Cek database: `borrowType = 'equipment'`, `quantity = 1`

---

## Rollback (Jika Ada Masalah)

SQLite tidak support `DROP COLUMN`, jadi rollback harus dengan:

1. Backup data:
```sql
CREATE TABLE loans_backup AS SELECT * FROM loans;
```

2. Hapus tabel lama:
```sql
DROP TABLE loans;
```

3. Buat ulang tanpa kolom baru:
```sql
CREATE TABLE loans (
  id TEXT PRIMARY KEY,
  -- ... struktur lama ...
);
```

4. Copy data kembali:
```sql
INSERT INTO loans SELECT id, borrowerName, identity, unit, roomType, facilityId, startDate, endDate, notes, status, createdAt, updatedAt FROM loans_backup;
```

---

## File Terkait

- `db.js` - Definisi schema database (sudah diupdate)
- `migrate.sql` - SQL script untuk migrasi
- `migrate-add-borrow-type.js` - Node.js script (opsional, butuh dependencies)
