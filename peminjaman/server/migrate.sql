-- Migration: Add borrowType and quantity columns to loans table
-- Run this if you already have a database with data

-- Add borrowType column (default: 'room' for existing records)
ALTER TABLE loans ADD COLUMN borrowType TEXT DEFAULT 'room';

-- Add quantity column (default: 1 for existing records)
ALTER TABLE loans ADD COLUMN quantity INTEGER DEFAULT 1;

-- Update existing records to ensure they have default values
UPDATE loans SET borrowType = 'room' WHERE borrowType IS NULL;
UPDATE loans SET quantity = 1 WHERE quantity IS NULL;

-- Verify the changes
-- SELECT * FROM pragma_table_info('loans');
