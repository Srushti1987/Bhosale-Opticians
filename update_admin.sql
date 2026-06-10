-- Update Admin Credentials
-- Run this in phpMyAdmin to add phone number to admin

USE sunray_db;

-- First, add phone column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(15) DEFAULT NULL;

-- Update admin user with phone number (keeping old email: admin@sunray.com)
UPDATE users 
SET phone = '9960815363' 
WHERE role = 'admin';

-- Verify the update
SELECT id, name, email, phone, role FROM users WHERE role = 'admin';
