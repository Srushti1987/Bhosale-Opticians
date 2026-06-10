# SUNRAY - Premium Eyewear E-Commerce Website

A beautiful, modern eyewear e-commerce website built with PHP, HTML, CSS, JavaScript, Bootstrap 5, and MySQL.

## Features

✨ **Modern & Attractive Design**
- Gradient hero sections with animations
- Smooth hover effects and transitions
- Responsive design for all devices
- Beautiful product cards with sale badges

🛍️ **E-Commerce Functionality**
- Product catalog with database integration
- Shopping cart (localStorage)
- User authentication (login/register)
- Product filtering by category
- Sale products section

🎨 **UI/UX Enhancements**
- Smooth animations (fade-in, pulse effects)
- Card hover effects (lift up on hover)
- Modern gradient overlays
- Ripple button effects
- Sticky navigation header

## Installation Steps

### 1. Start XAMPP
- Open XAMPP Control Panel
- Start **Apache** and **MySQL**

### 2. Create Database
- Go to: http://localhost/phpmyadmin
- Click "Import" tab
- Choose the `database.sql` file
- Click "Go" to import

### 3. Copy Files
- Copy all project files to: `C:\xampp\htdocs\sunray\`

### 4. Access Website
- Open browser and go to: http://localhost/sunray/

## File Structure

```
sunray/
├── index.php           # Homepage
├── products.php        # Products listing page
├── cart.php           # Shopping cart page
├── login.php          # Login page
├── register.php       # Registration page
├── config.php         # Database configuration
├── database.sql       # Database schema and sample data
├── style.css          # Custom CSS styles
├── script.js          # JavaScript functionality
└── README.md          # This file
```

## Database Configuration

Edit `config.php` if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sunray_db');
```

## Demo Login Credentials

- **Email:** john@example.com
- **Password:** password123

## Technologies Used

- **Backend:** PHP 8.x
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Framework:** Bootstrap 5.3.2
- **Database:** MySQL 8.0
- **Fonts:** Google Fonts (Jost)
- **Icons:** Emoji icons

## Features Breakdown

### Homepage
- Animated hero section with dual CTAs
- Featured products from database
- Features section (shipping, payment, returns)
- Responsive footer

### Products Page
- All products from database
- Category filtering (Sunglasses, Eyeglasses)
- Sale products filter
- Add to cart functionality

### Shopping Cart
- View cart items
- Remove items
- Order summary
- Checkout button

### Authentication
- User registration with password hashing
- Login with session management
- Email validation
- Password confirmation

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

This project is open source and available for educational purposes.

## Support

For issues or questions, please contact: info@sunray.com

---

**Enjoy your beautiful new eyewear website! 🕶️**
