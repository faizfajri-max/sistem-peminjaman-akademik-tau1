# Backend PHP - Sistem Peminjaman Fasilitas Kampus

## 📦 Struktur File Backend

```
api/
├── config/
│   ├── config.php              # Konfigurasi umum (JWT, CORS, Upload)
│   └── database.php            # Konfigurasi koneksi database MySQL
│
├── lib/
│   ├── auth.php               # Library JWT & Authentication
│   └── response.php           # Helper untuk response JSON
│
├── routes/
│   ├── auth.php              # Endpoints: register, login, me
│   ├── facilities.php        # Endpoints: CRUD fasilitas
│   ├── loans.php             # Endpoints: CRUD peminjaman
│   ├── comments.php          # Endpoints: dokumentasi pengembalian
│   └── reports.php           # Endpoints: laporan & statistik
│
├── uploads/                   # Folder untuk upload foto
│   └── .gitkeep
│
├── .gitignore                # Git ignore config
├── .htaccess                 # Apache URL rewrite rules
├── database.sql              # SQL schema & seed data
├── generate_hash.php         # Helper generate password hash
├── index.php                 # Entry point & router
├── Postman_Collection.json   # Postman collection untuk testing
├── QUICKSTART.md            # Panduan cepat
├── README.md                # Dokumentasi utama
└── SETUP_GUIDE.md           # Panduan setup lengkap
```

## 🎯 Fitur Backend

### Authentication & Authorization
- ✅ Register pengguna baru
- ✅ Login dengan JWT token
- ✅ Role-based access (Admin, Staff, User)
- ✅ Password hashing dengan bcrypt
- ✅ Token validation & middleware

### Facilities Management
- ✅ List semua fasilitas (public)
- ✅ Detail fasilitas
- ✅ Create fasilitas (admin only)
- ✅ Update fasilitas (admin only)
- ✅ Delete fasilitas (admin only)
- ✅ Filter by type & search

### Loans Management
- ✅ Create peminjaman (authenticated user)
- ✅ List peminjaman dengan filter
- ✅ Detail peminjaman
- ✅ Update status (admin/staff only)
- ✅ Delete peminjaman (owner/admin)
- ✅ Relasi many-to-many dengan facilities

### Comments & Return Documentation
- ✅ Add comment untuk peminjaman
- ✅ Upload multiple photos
- ✅ List comments per loan
- ✅ Mark as returned (admin/staff)
- ✅ Delete comment (owner/admin)

### Reports & Statistics
- ✅ Summary dashboard (admin/staff)
- ✅ User statistics
- ✅ Facility usage reports
- ✅ Loans by month, type, status

### Additional Features
- ✅ CORS support untuk frontend
- ✅ Clean URL dengan .htaccess
- ✅ Error handling & validation
- ✅ File upload dengan validation
- ✅ JSON response format

## 🗄️ Database Schema

### Tables

**users**
- id (INT, PK)
- name (VARCHAR)
- email (VARCHAR, UNIQUE)
- password (VARCHAR, hashed)
- role (ENUM: admin, staff, user)
- created_at, updated_at

**facilities**
- id (INT, PK)
- name (VARCHAR)
- type (VARCHAR)
- capacity (INT)
- location (VARCHAR)
- features (JSON)
- created_at, updated_at

**loans**
- id (INT, PK)
- user_id (INT, FK)
- unit (VARCHAR)
- purpose (TEXT)
- start_date, end_date (DATE)
- start_time, end_time (TIME)
- room_type (VARCHAR)
- participants (INT)
- notes (TEXT)
- status (ENUM: pending, approved, rejected, done)
- created_at, updated_at

**loan_facilities** (junction table)
- id (INT, PK)
- loan_id (INT, FK)
- facility_id (INT, FK)
- created_at

**comments**
- id (INT, PK)
- loan_id (INT, FK)
- user_id (INT, FK)
- comment (TEXT)
- photos (JSON)
- created_at

## 🔌 API Endpoints

### Authentication
```
POST   /api/auth/register       Register user baru
POST   /api/auth/login          Login & dapat token
GET    /api/auth/me             Info user saat ini
```

### Facilities
```
GET    /api/facilities          List semua fasilitas
GET    /api/facilities/:id      Detail fasilitas
POST   /api/facilities          Tambah fasilitas (admin)
PUT    /api/facilities/:id      Update fasilitas (admin)
DELETE /api/facilities/:id      Hapus fasilitas (admin)
```

### Loans
```
GET    /api/loans               List peminjaman
GET    /api/loans/:id           Detail peminjaman
POST   /api/loans               Buat peminjaman (auth)
PATCH  /api/loans/:id/status    Update status (admin/staff)
DELETE /api/loans/:id           Hapus peminjaman
```

### Comments
```
GET    /api/comments/:loanId              List comments
POST   /api/comments/:loanId              Add comment + foto
PATCH  /api/comments/:loanId/mark-returned   Mark selesai
DELETE /api/comments/:commentId          Hapus comment
```

### Reports
```
GET    /api/reports/summary           Dashboard stats (admin/staff)
GET    /api/reports/user-stats        Stats per user
GET    /api/reports/facility-usage    Usage per facility
```

## 🔐 Security Features

1. **Password Hashing**: Bcrypt dengan cost factor 10
2. **JWT Authentication**: Token expires dalam 7 hari
3. **Role-based Access**: Middleware untuk cek role
4. **Input Validation**: Validasi semua input dari user
5. **SQL Injection Prevention**: Prepared statements dengan PDO
6. **File Upload Validation**: 
   - Max size: 5MB
   - Allowed types: jpg, jpeg, png, gif
   - Unique filename generation
7. **CORS Configuration**: Customizable per environment

## 📱 Frontend Integration

Backend ini kompatibel dengan frontend yang sudah ada. Update URL API di JavaScript:

```javascript
// Ganti dari Node.js backend:
const API = 'http://localhost:4000/api';

// Ke PHP backend:
const API = 'http://localhost/peminjaman/api';
```

## 🚀 Deployment Checklist

Untuk production:

- [ ] Ganti JWT_SECRET dengan string yang kuat
- [ ] Update CORS_ORIGIN ke domain frontend
- [ ] Disable display_errors di PHP
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Backup database secara rutin
- [ ] Gunakan environment variables untuk config
- [ ] Implement rate limiting
- [ ] Add logging system
- [ ] Setup monitoring

## 📊 Performance Tips

1. **Database Indexing**: Index sudah ditambahkan di kolom yang sering di-query
2. **Caching**: Implement Redis/Memcached untuk production
3. **Query Optimization**: Gunakan JOIN dengan bijak
4. **File Upload**: Compress images sebelum upload
5. **API Response**: Pagination untuk list endpoints

## 🧪 Testing

### Manual Testing
1. Import `Postman_Collection.json` ke Postman
2. Test semua endpoint
3. Verify authentication & authorization
4. Test file upload
5. Check error handling

### Tools
- **Postman**: REST API testing
- **phpMyAdmin**: Database management
- **Chrome DevTools**: Network inspection
- **VS Code REST Client**: Alternative testing tool

## 📚 Documentation Files

1. **QUICKSTART.md** - Panduan cepat 5 menit
2. **SETUP_GUIDE.md** - Panduan setup lengkap & troubleshooting
3. **README.md** - Dokumentasi utama API
4. **Postman_Collection.json** - Collection untuk testing
5. **database.sql** - Schema & seed data

## 🔄 Version History

### v1.0.0 (Current)
- Initial release
- Full REST API implementation
- JWT authentication
- File upload support
- Role-based access control
- Complete CRUD operations
- Reports & statistics

## 👨‍💻 Development

### Requirements
- PHP 7.4+
- MySQL 5.7+
- Apache with mod_rewrite

### Local Development
```bash
# Clone/Download project
cd c:\xamppp\htdocs\peminjaman

# Start XAMPP
# Import database.sql
# Generate password hash
php api/generate_hash.php

# Test API
curl http://localhost/peminjaman/api/
```

### Code Style
- PSR-12 coding standard
- Camel case untuk variables
- Pascal case untuk classes
- Descriptive function names
- Comments di Indonesia

## 📞 Support

Jika ada masalah:
1. Baca troubleshooting di SETUP_GUIDE.md
2. Cek error log di XAMPP
3. Verify database connection
4. Test dengan Postman collection

## 📄 License

MIT License - Free untuk keperluan edukasi

---

**Backend PHP siap digunakan!** 🎉

Untuk memulai, baca [QUICKSTART.md](QUICKSTART.md)
