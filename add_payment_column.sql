-- Migration to add payment_id column to orders table
-- Run this if you already have the database set up

USE sunray_db;

-- Add payment_id column to orders table
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_id VARCHAR(255) DEFAULT NULL;

-- Verify the column was added
DESCRIBE orders;
