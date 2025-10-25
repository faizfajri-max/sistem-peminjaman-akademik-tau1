-- Create bookings table for public form submissions
-- This table stores peminjaman from borrow.html form

USE peminjaman_db;

-- Table: bookings (peminjaman dari form publik)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) NOT NULL UNIQUE,
    borrower_name VARCHAR(255) NOT NULL,
    identity VARCHAR(100) NOT NULL,
    unit VARCHAR(255) NOT NULL,
    facility_id INT NOT NULL,
    facility_name VARCHAR(255) NOT NULL,
    room_type VARCHAR(100),
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    notes TEXT,
    document_name VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected', 'done') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_booking_id (booking_id),
    INDEX idx_facility_id (facility_id),
    INDEX idx_status (status),
    INDEX idx_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: booking_facilities (fasilitas tambahan untuk booking)
CREATE TABLE IF NOT EXISTS booking_facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    item VARCHAR(100) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    INDEX idx_booking_id (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample booking data
INSERT INTO bookings (booking_id, borrower_name, identity, unit, facility_id, facility_name, room_type, start_date, end_date, notes, status) VALUES
('BK_it6de9d6i', 'Budi Santoso', '123456789', 'Himpunan Mahasiswa Informatika', 20, 'Studio Podcast', 'Ruang Podcast', '2025-10-27 10:00:00', '2025-10-27 12:00:00', 'Podcast recording session', 'approved'),
('BK_q1i924j6i', 'Ani Wijaya', '987654321', 'Unit Kegiatan Fotografi', 15, 'Ballroom Kampus', 'Ballroom', '2025-10-28 14:00:00', '2025-10-28 18:00:00', 'Workshop Photography', 'approved'),
('BK_6fh3qucyf', 'Dedi Prasetyo', '456789123', 'Fakultas Ekonomi', 15, 'Ballroom Kampus', 'Ballroom', '2025-10-30 09:00:00', '2025-10-30 12:00:00', 'Seminar Kewirausahaan', 'pending');

-- Insert sample facilities for bookings
-- Booking 1 facilities
INSERT INTO booking_facilities (booking_id, item, quantity) VALUES
(1, 'Mic', 2),
(1, 'HDMI', 1);

-- Booking 2 facilities
INSERT INTO booking_facilities (booking_id, item, quantity) VALUES
(2, 'Infocus', 1),
(2, 'Kursi', 50),
(2, 'Meja Bundar', 10);

-- Booking 3 facilities
INSERT INTO booking_facilities (booking_id, item, quantity) VALUES
(3, 'Infocus', 1),
(3, 'Mic', 2),
(3, 'Kursi', 100);

SELECT 'Bookings tables created successfully!' as message;
