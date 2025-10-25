# Changelog - Backend PHP API

All notable changes to this project will be documented in this file.

## [1.0.0] - 2025-10-24

### 🎉 Initial Release

#### Added
- **Authentication System**
  - User registration with email validation
  - Login with JWT token generation
  - Password hashing with bcrypt
  - Token-based authentication middleware
  - Role-based access control (Admin, Staff, User)

- **Facilities Management**
  - List all facilities (public access)
  - Get facility details
  - Create new facility (admin only)
  - Update facility (admin only)
  - Delete facility (admin only)
  - Filter by type and search functionality

- **Loans Management**
  - Create new loan request (authenticated users)
  - List loans with filters (status, user, date range, room type)
  - Get loan details with facilities
  - Update loan status (admin/staff only)
  - Delete loan (owner or admin)
  - Many-to-many relationship with facilities

- **Comments & Return Documentation**
  - Add comments to loans
  - Upload multiple photos (max 5MB each)
  - List comments per loan
  - Mark loan as returned (admin/staff)
  - Delete comments (owner or admin)

- **Reports & Statistics**
  - Dashboard summary (admin/staff only)
  - Loans statistics by status, month, type
  - User statistics
  - Facility usage reports
  - Popular facilities tracking

- **Core Features**
  - RESTful API architecture
  - Clean URL routing with .htaccess
  - CORS support for frontend integration
  - JSON response format
  - Error handling and validation
  - SQL injection prevention with PDO prepared statements
  - File upload validation

#### Database
- Created MySQL schema with 5 tables:
  - `users` - User accounts with roles
  - `facilities` - Available facilities/rooms
  - `loans` - Borrowing requests
  - `loan_facilities` - Junction table for many-to-many
  - `comments` - Return documentation with photos

- Seed data included:
  - 3 default users (admin, staff, user)
  - 22 facilities (14 classrooms + 8 special facilities)
  - Sample loans and comments

#### Documentation
- README.md - Main API documentation
- SETUP_GUIDE.md - Complete installation guide
- QUICKSTART.md - 5-minute quick start guide
- BACKEND_SUMMARY.md - Technical overview
- INSTALLATION_CHECKLIST.md - Step-by-step verification
- Postman_Collection.json - Ready-to-use API testing collection

#### Security
- JWT token with 7-day expiration
- Password hashing with bcrypt
- Role-based middleware
- Input validation and sanitization
- File upload restrictions (type, size)
- Prepared statements for SQL queries
- Error logging (production-ready)

#### Developer Tools
- generate_hash.php - Password hash generator
- Production config examples
- .gitignore for sensitive files
- Comprehensive error messages

### Technical Details
- **PHP Version**: 7.4+
- **Database**: MySQL 5.7+
- **Server**: Apache with mod_rewrite
- **Architecture**: MVC-inspired with routes and controllers
- **Authentication**: JWT (JSON Web Tokens)
- **Response Format**: JSON

### API Endpoints (15 total)
- **Auth**: 3 endpoints (register, login, me)
- **Facilities**: 5 endpoints (list, detail, create, update, delete)
- **Loans**: 5 endpoints (list, detail, create, update status, delete)
- **Comments**: 4 endpoints (list, create, mark returned, delete)
- **Reports**: 3 endpoints (summary, user stats, facility usage)

### Known Limitations
- No pagination implemented yet (will be added in v1.1.0)
- No email notifications (will be added in v1.2.0)
- No real-time updates (consider WebSocket in future)
- File upload limited to images only

---

## Upcoming Features (Roadmap)

### [1.1.0] - Planned
- [ ] Pagination for list endpoints
- [ ] Advanced search and filtering
- [ ] Bulk operations for admin
- [ ] Export reports to PDF/Excel
- [ ] API rate limiting

### [1.2.0] - Planned
- [ ] Email notifications (approval, rejection, reminders)
- [ ] SMS notifications (optional)
- [ ] Calendar integration
- [ ] Conflict detection for bookings
- [ ] Waiting list system

### [1.3.0] - Planned
- [ ] Real-time availability updates
- [ ] Mobile app support endpoints
- [ ] QR code generation for loans
- [ ] Digital signature for returns
- [ ] Advanced analytics dashboard

### [2.0.0] - Future
- [ ] Multi-tenant support
- [ ] API versioning
- [ ] GraphQL support
- [ ] WebSocket for real-time features
- [ ] Machine learning for recommendations

---

## Migration Guide

### From Node.js Backend
If migrating from the Node.js backend:

1. Update frontend API URL:
   ```javascript
   // From:
   const API = 'http://localhost:4000/api';
   // To:
   const API = 'http://localhost/peminjaman/api';
   ```

2. Response format remains the same (JSON)
3. Token format and validation compatible
4. All endpoints maintain same structure

---

## Support
For issues, questions, or contributions:
- Read SETUP_GUIDE.md for troubleshooting
- Check INSTALLATION_CHECKLIST.md for verification
- Use Postman_Collection.json for testing

---

**Version**: 1.0.0  
**Release Date**: October 24, 2025  
**Status**: Stable  
**License**: MIT
