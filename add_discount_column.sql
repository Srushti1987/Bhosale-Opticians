-- Add discount_percent column to products table
ALTER TABLE products ADD COLUMN discount_percent INT DEFAULT 0 AFTER on_sale;

-- Update existing sale products with different discount percentages
UPDATE products SET discount_percent = 20 WHERE on_sale = 1 AND id % 5 = 0;
UPDATE products SET discount_percent = 15 WHERE on_sale = 1 AND id % 5 = 1;
UPDATE products SET discount_percent = 25 WHERE on_sale = 1 AND id % 5 = 2;
UPDATE products SET discount_percent = 30 WHERE on_sale = 1 AND id % 5 = 3;
UPDATE products SET discount_percent = 10 WHERE on_sale = 1 AND id % 5 = 4;

-- Verify the changes
SELECT id, name, price, on_sale, discount_percent FROM products WHERE on_sale = 1 LIMIT 10;
