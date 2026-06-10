-- ========================================
-- QUICK FIX: Add Phone Column
-- ========================================
-- Run this in phpMyAdmin SQL tab
-- Database: sunray_db
-- ========================================

USE sunray_db;

-- Add phone column to users table
ALTER TABLE users ADD COLUMN phone VARCHAR(15) DEFAULT NULL;

-- Update admin with phone number
UPDATE users SET phone = '9960815363' WHERE role = 'admin';

-- Verify the changes
SELECT id, name, email, phone, role FROM users;

-- ========================================
-- Done! Now you can register with phone numbers
-- ========================================
