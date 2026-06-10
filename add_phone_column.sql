-- Migration to add phone column to users table and update admin
-- Run this if you already have the database set up

USE sunray_db;

-- Add phone column to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(15) DEFAULT NULL;

-- Update admin user with phone number (keeping old email)
UPDATE users SET phone = '9960815363' WHERE role = 'admin';

-- Verify the changes
SELECT id, name, email, phone, role FROM users WHERE role = 'admin';
