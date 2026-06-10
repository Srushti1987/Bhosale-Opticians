# BHOSALE OPTICIANS E-COMMERCE SYSTEM
## Entity Relationship Diagram (ERD)

**Database Name:** sunray_db  
**Project:** Bhosale Opticians E-Commerce System  
**Version:** 2.0  
**Date:** March 7, 2026

---

## DATABASE SCHEMA OVERVIEW

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    BHOSALE OPTICIANS E-COMMERCE DATABASE                        │
│                              (sunray_db)                                       │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     USERS       │    │   PASSWORD_     │    │    PRODUCTS     │    │    FEEDBACK     │
│                 │    │    RESETS       │    │                 │    │                 │
│ • id (PK)       │    │ • id (PK)       │    │ • id (PK)       │    │ • id (PK)       │
│ • name          │    │ • email         │    │ • name          │    │ • user_id (FK)  │
│ • email (UQ)    │    │ • token         │    │ • description   │    │ • order_id (FK) │
│ • password      │    │ • created_at    │    │ • price         │    │ • rating        │
│ • phone         │    │ • expires_at    │    │ • category      │    │ • feedback_text │
│ • role          │    │                 │    │ • gender        │    │ • created_at    │
│ • created_at    │    │                 │    │ • image_url     │    │                 │
│                 │    │                 │    │ • on_sale       │    │                 │
└─────────────────┘    └─────────────────┘    │ • discount_%    │    └─────────────────┘
         │                                     │ • stock         │             │
         │                                     │ • badge         │             │
         │                                     │ • created_at    │             │
         │                                     │                 │             │
         │                                     └─────────────────┘             │
         │                                              │                      │
         │                                              │                      │
         │              ┌─────────────────┐            │                      │
         │              │     ORDERS      │            │                      │
         │              │                 │            │                      │
         │              │ • id (PK)       │            │                      │
         └──────────────│ • user_id (FK)  │            │                      │
                        │ • total_amount  │            │                      │
                        │ • status        │            │                      │
                        │ • shipping_addr │            │                      │
                        │ • payment_id    │            │                      │
                        │ • created_at    │            │                      │
                        │                 │            │                      │
                        └─────────────────┘            │                      │
                                 │                     │                      │
                                 │                     │                      │
                        ┌─────────────────┐            │                      │
                        │   ORDER_ITEMS   │            │                      │
                        │                 │            │                      │
                        │ • id (PK)       │            │                      │
                        │ • order_id (FK) │────────────┘                      │
                        │ • product_id(FK)│───────────────────────────────────┘
                        │ • quantity      │
                        │ • price         │
                        │                 │
                        └─────────────────┘
```

---

## DETAILED TABLE STRUCTURES

### 1. USERS TABLE
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15) DEFAULT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Purpose:** Store user account information for customers and administrators  
**Key Features:**
- Unique email constraint
- Role-based access (user/admin)
- Encrypted password storage
- Optional phone number

### 2. PRODUCTS TABLE
```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    image_url VARCHAR(255),
    on_sale BOOLEAN DEFAULT FALSE,
    discount_percent INT DEFAULT 0,
    stock INT DEFAULT 0,
    badge VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Purpose:** Store eyewear product catalog  
**Key Features:**
- Variable discount percentages (10%-30%)
- Stock management with real-time tracking
- Product badges (Bestseller, New Arrival, etc.)
- Category and gender filtering
- Sale status management

### 3. ORDERS TABLE
```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    shipping_address TEXT,
    payment_id VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Purpose:** Store customer order information  
**Key Features:**
- Links to user account
- Order status tracking
- Shipping address storage
- Payment method support (COD)

### 4. ORDER_ITEMS TABLE
```sql
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

**Purpose:** Store individual items within each order  
**Key Features:**
- Many-to-many relationship between orders and products
- Quantity and price per item
- Historical price preservation

### 5. FEEDBACK TABLE
```sql
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT DEFAULT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    feedback_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

**Purpose:** Store customer feedback and ratings  
**Key Features:**
- 5-star rating system
- Links to specific orders (optional)
- User-specific feedback tracking
- Detailed text reviews

### 6. PASSWORD_RESETS TABLE
```sql
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL
);
```

**Purpose:** Manage password reset tokens  
**Key Features:**
- Secure token generation
- 1-hour expiration
- Email-based reset system

---

## RELATIONSHIPS DIAGRAM

```
USERS (1) ──────────── (M) ORDERS
  │                        │
  │                        │
  │                   ORDER_ITEMS (M) ──────────── (1) PRODUCTS
  │                        │
  │                        │
  └─────────── (M) FEEDBACK (M) ──────────────────────┘
                    │
                    │
               (Optional Link)
```

### RELATIONSHIP DETAILS:

1. **USERS → ORDERS** (One-to-Many)
   - One user can have multiple orders
   - Each order belongs to one user
   - Foreign Key: `orders.user_id → users.id`

2. **ORDERS → ORDER_ITEMS** (One-to-Many)
   - One order can contain multiple items
   - Each order item belongs to one order
   - Foreign Key: `order_items.order_id → orders.id`

3. **PRODUCTS → ORDER_ITEMS** (One-to-Many)
   - One product can be in multiple order items
   - Each order item references one product
   - Foreign Key: `order_items.product_id → products.id`

4. **USERS → FEEDBACK** (One-to-Many)
   - One user can provide multiple feedback entries
   - Each feedback belongs to one user
   - Foreign Key: `feedback.user_id → users.id`

5. **ORDERS → FEEDBACK** (One-to-Many, Optional)
   - One order can have multiple feedback entries
   - Feedback can be general (not linked to specific order)
   - Foreign Key: `feedback.order_id → orders.id` (NULL allowed)

---

## BUSINESS RULES

### USER MANAGEMENT:
- Email addresses must be unique
- Users can be either 'user' or 'admin' role
- Admin accounts can only be created via database
- Password minimum length: 6 characters

### PRODUCT MANAGEMENT:
- Products categorized as 'Eyeglasses' or 'Sunglasses'
- Gender filtering: 'Men', 'Women', 'Kids'
- Stock automatically decreases when orders are placed
- Discount percentages: 10%, 15%, 20%, 25%, 30%
- Low stock alert when stock ≤ 10

### ORDER MANAGEMENT:
- Orders start with 'pending' status
- Total amount includes 18% GST
- Payment method: Cash on Delivery (COD)
- Stock validation at checkout

### FEEDBACK SYSTEM:
- Rating scale: 1-5 stars (required)
- Feedback text is required
- Can be linked to specific orders
- Users cannot submit duplicate feedback for same order

### SECURITY:
- Passwords hashed using bcrypt
- Password reset tokens expire after 1 hour
- SQL injection prevention with prepared statements
- XSS protection with input sanitization

---

## INDEXES AND CONSTRAINTS

### PRIMARY KEYS:
- All tables have auto-incrementing integer primary keys

### FOREIGN KEY CONSTRAINTS:
- `orders.user_id` → `users.id`
- `order_items.order_id` → `orders.id`
- `order_items.product_id` → `products.id`
- `feedback.user_id` → `users.id`
- `feedback.order_id` → `orders.id`

### UNIQUE CONSTRAINTS:
- `users.email` (unique email addresses)

### CHECK CONSTRAINTS:
- `feedback.rating` (between 1 and 5)
- `users.role` (enum: 'user', 'admin')

---

## SAMPLE DATA OVERVIEW

### PRODUCTS: 70+ Items
- **Men's Collection:** 25 products
- **Women's Collection:** 25 products  
- **Kids Collection:** 20 products
- **Categories:** Eyeglasses, Sunglasses
- **Badges:** Bestseller, New Arrival, Trending, Premium, Sale, Hot Deal, Limited, Eco-Friendly

### USERS: Default Accounts
- **Admin:** admin@sunray.com (role: admin)
- **Test User:** john@example.com (role: user)

---

## DATABASE STATISTICS

| Table | Estimated Rows | Storage Type |
|-------|---------------|--------------|
| users | 100-1000 | InnoDB |
| products | 70+ | InnoDB |
| orders | 500-5000 | InnoDB |
| order_items | 1000-10000 | InnoDB |
| feedback | 200-2000 | InnoDB |
| password_resets | 10-100 | InnoDB |

**Total Database Size:** ~5-50 MB (depending on usage)  
**Storage Engine:** InnoDB (supports foreign keys and transactions)  
**Character Set:** utf8mb4 (full Unicode support)

---

**Document Created:** March 7, 2026  
**Project:** Bhosale Opticians E-Commerce System  
**Version:** 2.0  
**Status:** Production Ready