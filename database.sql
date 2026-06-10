-- Create Database
CREATE DATABASE IF NOT EXISTS sunray_db;
USE sunray_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15) DEFAULT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Password Reset Tokens Table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    image_url VARCHAR(255),
    on_sale BOOLEAN DEFAULT FALSE,
    stock INT DEFAULT 0,
    badge VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    shipping_address TEXT,
    payment_id VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert Sample Products
INSERT INTO products (name, description, price, category, gender, image_url, on_sale, stock) VALUES
-- MEN'S COLLECTION
('Classic Aviator Gold', 'Premium aviator sunglasses with UV protection and gold frame', 2999.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 50),
('Vintage Round Silver', 'Retro round frames perfect for any occasion', 2799.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 40),
('Sport Shield Blue', 'High-performance sports sunglasses for active lifestyle', 3799.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1577803645773-f96470509666?w=400', TRUE, 25),
('Pilot Premium Brown', 'Premium pilot sunglasses with metal frame', 4299.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1473496169904-658ba7c44d8a?w=400', FALSE, 20),
('Executive Black', 'Professional eyeglasses for business look', 3599.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 40),
('Sporty Wrap Red', 'Wraparound sports sunglasses for outdoor activities', 3299.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', TRUE, 35),
('Classic Wayfarer Black', 'Timeless wayfarer design for everyday wear', 2899.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 45),
('Metal Frame Silver', 'Lightweight metal frames with anti-glare coating', 3199.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 30),
('Polarized Driving', 'Polarized lenses perfect for driving', 4599.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 25),
('Smart Blue Light', 'Blue light blocking glasses for screen time', 2499.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 50),

-- WOMEN'S COLLECTION
('Modern Wayfarer Black', 'Contemporary wayfarer style with polarized lenses', 3499.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', TRUE, 30),
('Cat Eye Elegance Pink', 'Elegant cat-eye frames for a sophisticated look', 3299.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1508296695146-257a814070b4?w=400', FALSE, 35),
('Square Classic Red', 'Classic square frames for everyday wear', 2599.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 45),
('Oversized Glam Purple', 'Oversized frames for a glamorous look', 3999.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 30),
('Designer Butterfly', 'Luxury butterfly frames with crystal details', 4999.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 15),
('Vintage Cat Eye Tortoise', 'Retro tortoise shell cat-eye frames', 3799.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1508296695146-257a814070b4?w=400', TRUE, 28),
('Round Gradient Rose', 'Round frames with gradient rose gold finish', 3599.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 32),
('Chic Rectangle White', 'Modern rectangular frames in pearl white', 2899.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 40),
('Luxury Oversized Gold', 'Premium oversized sunglasses with gold accents', 5499.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 20),
('Elegant Oval Silver', 'Delicate oval frames in brushed silver', 3199.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 35),

-- KIDS COLLECTION
('Kids Fun Yellow', 'Colorful and durable frames for kids', 1499.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 60),
('Kids Sport Green', 'Sporty sunglasses designed for active kids', 1799.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', TRUE, 50),
('Kids Rainbow Blue', 'Fun rainbow design with flexible frames', 1599.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 55),
('Kids Adventure Red', 'Durable adventure-ready sunglasses', 1899.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 45),
('Kids Cartoon Pink', 'Cute cartoon character frames for girls', 1399.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 65),
('Kids Superhero Black', 'Cool superhero themed sunglasses', 1699.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 50),
('Kids Flexible Orange', 'Super flexible and unbreakable frames', 1799.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 48),
('Kids Cool Dude Blue', 'Trendy blue frames for cool kids', 1599.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', TRUE, 52);

-- Insert Sample User (password: password123)
INSERT INTO users (name, email, password, phone, role) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'user'),
('Admin User', 'admin@sunray.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9960815363', 'admin');
