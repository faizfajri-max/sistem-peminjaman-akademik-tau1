-- Create test accounts for Student, Admin, and Staff
-- Run this in phpMyAdmin or MySQL CLI

USE peminjaman_db;

-- Insert student user
INSERT INTO users (name, email, password, role, created_at) 
VALUES (
    'John Student',
    'john@student.tau.ac.id',
    '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', -- password: admin123
    'user',
    NOW()
);

-- Insert admin user
INSERT INTO users (name, email, password, role, created_at) 
VALUES (
    'Admin User',
    'admin@admin.tau.ac.id',
    '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', -- password: admin123
    'admin',
    NOW()
);

-- Insert staff user
INSERT INTO users (name, email, password, role, created_at) 
VALUES (
    'Staff User',
    'staff@staff.tau.ac.id',
    '$2y$10$PhrXZqeNq9KFoKDBg1f8euIxUY9v2/9qinB6vNwZZxFBbaNj/GSWS', -- password: admin123
    'staff',
    NOW()
);

-- Check existing users
SELECT id, name, email, role FROM users;
