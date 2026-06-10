-- Add badge column to products table
ALTER TABLE products ADD COLUMN IF NOT EXISTS badge VARCHAR(50) DEFAULT NULL;

-- Clear existing products (optional - remove this line if you want to keep existing products)
-- TRUNCATE TABLE products;

-- Insert More Products with Badges
INSERT INTO products (name, description, price, category, gender, image_url, on_sale, stock, badge) VALUES

-- MEN'S COLLECTION (25 products)
('Classic Aviator Gold', 'Premium aviator sunglasses with UV protection and gold frame', 2999.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 50, 'Bestseller'),
('Vintage Round Silver', 'Retro round frames perfect for any occasion', 2799.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 40, 'Trending'),
('Sport Shield Blue', 'High-performance sports sunglasses for active lifestyle', 3799.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1577803645773-f96470509666?w=400', TRUE, 25, 'Sale'),
('Pilot Premium Brown', 'Premium pilot sunglasses with metal frame', 4299.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1473496169904-658ba7c44d8a?w=400', FALSE, 20, 'New Arrival'),
('Executive Black', 'Professional eyeglasses for business look', 3599.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 40, NULL),
('Sporty Wrap Red', 'Wraparound sports sunglasses for outdoor activities', 3299.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', TRUE, 35, 'Hot Deal'),
('Classic Wayfarer Black', 'Timeless wayfarer design for everyday wear', 2899.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 45, 'Bestseller'),
('Metal Frame Silver', 'Lightweight metal frames with anti-glare coating', 3199.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 30, 'Limited'),
('Polarized Driving', 'Polarized lenses perfect for driving', 4599.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 25, 'Premium'),
('Smart Blue Light', 'Blue light blocking glasses for screen time', 2499.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 50, 'Trending'),
('Titanium Flex Black', 'Ultra-lightweight titanium frames with flex hinges', 5299.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 15, 'Premium'),
('Clubmaster Classic', 'Iconic clubmaster style with modern twist', 3499.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 30, 'Bestseller'),
('Rimless Minimalist', 'Sleek rimless design for subtle elegance', 4199.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 20, 'New Arrival'),
('Photochromic Transition', 'Auto-tinting lenses that adapt to light', 5799.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 18, 'Premium'),
('Retro Square Brown', 'Vintage-inspired square frames in tortoise shell', 2999.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 35, 'Sale'),
('Mirrored Aviator Blue', 'Stylish mirrored aviators with blue tint', 3899.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 28, 'Trending'),
('Carbon Fiber Sport', 'Lightweight carbon fiber sports glasses', 6299.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1577803645773-f96470509666?w=400', FALSE, 12, 'Premium'),
('Wooden Frame Natural', 'Eco-friendly wooden frames handcrafted', 4799.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 15, 'Eco-Friendly'),
('Gradient Lens Fashion', 'Trendy gradient lens sunglasses', 3299.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', TRUE, 25, 'Hot Deal'),
('Progressive Multifocal', 'Advanced progressive lenses for all distances', 6999.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 10, 'Premium'),
('Magnetic Clip-On', 'Eyeglasses with magnetic sunglasses clip-on', 4499.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 22, 'New Arrival'),
('Hexagonal Vintage Gold', 'Unique hexagonal frames in gold finish', 3799.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 20, 'Trending'),
('Anti-Fog Sports', 'Anti-fog coating for sports and outdoor use', 3999.00, 'Sunglasses', 'Men', 'https://images.unsplash.com/photo-1577803645773-f96470509666?w=400', FALSE, 30, 'Bestseller'),
('Designer Acetate', 'Premium Italian acetate frames', 5499.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 15, 'Premium'),
('Foldable Travel', 'Compact foldable glasses perfect for travel', 2799.00, 'Eyeglasses', 'Men', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 40, 'Limited'),

-- WOMEN'S COLLECTION (25 products)
('Modern Wayfarer Black', 'Contemporary wayfarer style with polarized lenses', 3499.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', TRUE, 30, 'Bestseller'),
('Cat Eye Elegance Pink', 'Elegant cat-eye frames for a sophisticated look', 3299.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1508296695146-257a814070b4?w=400', FALSE, 35, 'Trending'),
('Square Classic Red', 'Classic square frames for everyday wear', 2599.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 45, 'Sale'),
('Oversized Glam Purple', 'Oversized frames for a glamorous look', 3999.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 30, 'New Arrival'),
('Designer Butterfly', 'Luxury butterfly frames with crystal details', 4999.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 15, 'Premium'),
('Vintage Cat Eye Tortoise', 'Retro tortoise shell cat-eye frames', 3799.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1508296695146-257a814070b4?w=400', TRUE, 28, 'Hot Deal'),
('Round Gradient Rose', 'Round frames with gradient rose gold finish', 3599.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 32, 'Bestseller'),
('Chic Rectangle White', 'Modern rectangular frames in pearl white', 2899.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 40, 'Limited'),
('Luxury Oversized Gold', 'Premium oversized sunglasses with gold accents', 5499.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 20, 'Premium'),
('Elegant Oval Silver', 'Delicate oval frames in brushed silver', 3199.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 35, 'Trending'),
('Heart-Shaped Cute', 'Adorable heart-shaped sunglasses', 2499.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 25, 'Trending'),
('Rhinestone Glamour', 'Sparkling rhinestone-embellished frames', 4599.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 18, 'Premium'),
('Transparent Trendy', 'Modern transparent frames in pastel colors', 2799.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 40, 'New Arrival'),
('Floral Pattern Unique', 'Unique floral pattern on frame temples', 3399.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1508296695146-257a814070b4?w=400', FALSE, 22, 'Limited'),
('Geometric Modern', 'Bold geometric shapes for fashion-forward look', 3899.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 28, 'Trending'),
('Pearl Accent Luxury', 'Elegant frames with pearl accents', 5299.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 12, 'Premium'),
('Retro Round Colorful', 'Vibrant colored round frames', 2999.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 35, 'Sale'),
('Winged Cat Eye', 'Dramatic winged cat-eye sunglasses', 3699.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 25, 'Bestseller'),
('Minimalist Thin', 'Ultra-thin minimalist frames', 3299.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 30, 'Trending'),
('Gradient Butterfly', 'Butterfly frames with gradient lenses', 4199.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 20, 'New Arrival'),
('Rose Gold Aviator', 'Feminine rose gold aviator sunglasses', 3999.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400', FALSE, 28, 'Bestseller'),
('Marble Pattern Chic', 'Trendy marble pattern frames', 3499.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 25, 'Limited'),
('Oversized Square Bold', 'Bold oversized square sunglasses', 3799.00, 'Sunglasses', 'Women', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', TRUE, 30, 'Hot Deal'),
('Vintage Browline', 'Classic browline style frames', 3199.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 32, 'Trending'),
('Crystal Clear Fashion', 'Crystal clear frames with colored temples', 2899.00, 'Eyeglasses', 'Women', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 38, 'Sale'),

-- KIDS COLLECTION (20 products)
('Kids Fun Yellow', 'Colorful and durable frames for kids', 1499.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 60, 'Bestseller'),
('Kids Sport Green', 'Sporty sunglasses designed for active kids', 1799.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', TRUE, 50, 'Sale'),
('Kids Rainbow Blue', 'Fun rainbow design with flexible frames', 1599.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 55, 'Trending'),
('Kids Adventure Red', 'Durable adventure-ready sunglasses', 1899.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 45, 'New Arrival'),
('Kids Cartoon Pink', 'Cute cartoon character frames for girls', 1399.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 65, 'Hot Deal'),
('Kids Superhero Black', 'Cool superhero themed sunglasses', 1699.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 50, 'Bestseller'),
('Kids Flexible Orange', 'Super flexible and unbreakable frames', 1799.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 48, 'Premium'),
('Kids Cool Dude Blue', 'Trendy blue frames for cool kids', 1599.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', TRUE, 52, 'Limited'),
('Kids Princess Purple', 'Princess-themed frames with sparkles', 1499.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 55, 'Trending'),
('Kids Dinosaur Green', 'Fun dinosaur design for adventurous kids', 1699.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 45, 'New Arrival'),
('Kids Unicorn Magic', 'Magical unicorn-themed sunglasses', 1799.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 40, 'Bestseller'),
('Kids Robot Tech', 'Tech-inspired robot design frames', 1599.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 50, 'Trending'),
('Kids Butterfly Cute', 'Adorable butterfly-shaped sunglasses', 1499.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', TRUE, 48, 'Sale'),
('Kids Space Explorer', 'Space-themed frames for young explorers', 1699.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 42, 'New Arrival'),
('Kids Animal Print', 'Fun animal print pattern frames', 1599.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 55, 'Limited'),
('Kids Neon Bright', 'Bright neon colored sunglasses', 1799.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 45, 'Trending'),
('Kids Camouflage Cool', 'Cool camouflage pattern frames', 1699.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 40, 'Bestseller'),
('Kids Glitter Sparkle', 'Sparkly glitter frames for girls', 1599.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', TRUE, 50, 'Hot Deal'),
('Kids Sports Champion', 'Sporty wraparound sunglasses', 1899.00, 'Sunglasses', 'Kids', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', FALSE, 38, 'Premium'),
('Kids Emoji Fun', 'Fun emoji-themed frames', 1499.00, 'Eyeglasses', 'Kids', 'https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400', FALSE, 60, 'Trending');

-- Verify the products were added
SELECT COUNT(*) as total_products FROM products;
SELECT badge, COUNT(*) as count FROM products GROUP BY badge;
