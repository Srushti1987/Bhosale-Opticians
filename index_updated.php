<?php
session_start();
require_once 'config.php';

// Set page variables
$page_title = "Bhosale Opticians - Premium Eyewear";
$active_page = "home";
$base_url = "";

// Fetch products from database
$conn = getDBConnection();
$sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 6";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get maximum discount for sale banner
$max_discount_sql = "SELECT MAX(discount_percent) as max_discount FROM products WHERE on_sale = 1 AND discount_percent > 0";
$max_result = $conn->query($max_discount_sql);
$max_discount = 30; // Default
if ($max_result && $max_result->num_rows > 0) {
    $row = $max_result->fetch_assoc();
    $max_discount = $row['max_discount'] ?? 30;
}

$conn->close();

// Include header
include 'includes/header.php';
?>

    <!-- Hero Carousel Banner -->
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
        </div>
        
        <div class="carousel-inner">
            <!-- Slide 1 -->
            <div class="carousel-item active">
                <div class="hero-slide" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="container">
                        <div class="row align-items-center" style="min-height: 500px;">
                            <div class="col-lg-6 text-white">
                                <h1 class="display-3 fw-bold mb-4">Premium Eyewear Collection</h1>
                                <p class="lead mb-4">Discover the perfect blend of style and comfort with our exclusive range of eyeglasses and sunglasses.</p>
                                <a href="products.php" class="btn btn-light btn-lg px-5 py-3">Shop Now</a>
                            </div>
                            <div class="col-lg-6 text-center">
                                <img src="https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=600" alt="Eyewear" class="img-fluid" style="max-height: 400px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="carousel-item">
                <div class="hero-slide" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="container">
                        <div class="row align-items-center" style="min-height: 500px;">
                            <div class="col-lg-6 text-white">
                                <h1 class="display-3 fw-bold mb-4">Trending Sunglasses</h1>
                                <p class="lead mb-4">Protect your eyes in style with our latest collection of UV-protected sunglasses.</p>
                                <a href="products.php?category=Sunglasses" class="btn btn-light btn-lg px-5 py-3">Explore Collection</a>
                            </div>
                            <div class="col-lg-6 text-center">
                                <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600" alt="Sunglasses" class="img-fluid" style="max-height: 400px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="carousel-item">
                <div class="hero-slide" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="container">
                        <div class="row align-items-center" style="min-height: 500px;">
                            <div class="col-lg-6 text-white">
                                <h1 class="display-3 fw-bold mb-4">Special Offers</h1>
                                <p class="lead mb-4">Get up to <?= $max_discount ?>% off on selected eyewear. Limited time offer!</p>
                                <a href="products.php?sale=1" class="btn btn-light btn-lg px-5 py-3">View Deals</a>
                            </div>
                            <div class="col-lg-6 text-center">
                                <img src="https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=600" alt="Offers" class="img-fluid" style="max-height: 400px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <style>
        .carousel-fade .carousel-item {
            opacity: 0;
            transition: opacity 0.6s ease-in-out;
        }
        .carousel-fade .carousel-item.active {
            opacity: 1;
        }
        .hero-slide {
            padding: 60px 0;
        }
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
        }
        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
    </style>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <h1 class="display-1 fw-bold text-white mb-4 animate-fade-in">Discover Your Perfect Style</h1>
            <p class="lead text-white mb-5 animate-fade-in-delay">Premium Eyewear Collection for Men, Women & Kids</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap animate-fade-in-delay-2">
                <a href="products.php" class="btn btn-primary btn-lg px-5 py-3">Shop Now</a>
                <a href="products.php?sale=1" class="btn btn-outline-light btn-lg px-5 py-3">View Offers</a>
            </div>
        </div>
    </section>

    <!-- Category Cards -->
    <section class="container my-5 py-4">
        <h2 class="text-center mb-5 section-title">Shop by Category</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="products.php?gender=Men" class="text-decoration-none">
                    <div class="card category-card shadow-sm h-100">
                        <div class="card-body text-center p-5">
                            <div class="category-icon mb-3">👨</div>
                            <h3 class="mb-3">Men's Collection</h3>
                            <p class="text-muted">Stylish eyewear for modern men</p>
                            <span class="btn btn-outline-primary mt-3">Shop Men →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="products.php?gender=Women" class="text-decoration-none">
                    <div class="card category-card shadow-sm h-100">
                        <div class="card-body text-center p-5">
                            <div class="category-icon mb-3">👩</div>
                            <h3 class="mb-3">Women's Collection</h3>
                            <p class="text-muted">Elegant frames for every occasion</p>
                            <span class="btn btn-outline-primary mt-3">Shop Women →</span>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="products.php?gender=Kids" class="text-decoration-none">
                    <div class="card category-card shadow-sm h-100">
                        <div class="card-body text-center p-5">
                            <div class="category-icon mb-3">👶</div>
                            <h3 class="mb-3">Kids Collection</h3>
                            <p class="text-muted">Fun and durable eyewear for kids</p>
                            <span class="btn btn-outline-primary mt-3">Shop Kids →</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="container mb-5">
        <header class="row align-items-center mb-4">
            <div class="col-8">
                <h2 class="section-title">Featured Products</h2>
            </div>
            <div class="col-4 d-flex justify-content-end">
                <a href="products.php" class="text-muted">See More →</a>
            </div>
        </header>

        <?php if(count($products) > 0): ?>
        <div class="row g-4">
            <?php foreach($products as $product): ?>
            <div class="col-md-6 col-lg-4">
                <div class="product-card">
                    <?php if($product['on_sale']): ?>
                    <span class="sale-badge">SALE</span>
                    <?php endif; ?>
                    
                    <?php if(!empty($product['badge'])): ?>
                    <span class="product-badge badge-<?= strtolower(str_replace(' ', '-', $product['badge'])) ?>">
                        <?= htmlspecialchars($product['badge']) ?>
                    </span>
                    <?php endif; ?>
                    
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                    
                    <div class="product-body">
                        <h5 class="product-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="product-category text-muted">
                            <?= htmlspecialchars($product['category']) ?> • <?= htmlspecialchars($product['gender']) ?>
                        </p>
                        <p class="product-price">
                            <?php if($product['on_sale'] && $product['discount_percent'] > 0): ?>
                            <?php 
                                $discount = $product['discount_percent'];
                                $original_price = $product['price'] / (1 - ($discount / 100));
                            ?>
                            <span class="badge bg-danger mb-2"><?= $discount ?>% OFF</span><br>
                            <del class="text-danger">₹<?= number_format($original_price, 2) ?></del>
                            <?php endif; ?>
                            <span class="fw-bold fs-4 text-brown">₹<?= number_format($product['price'], 2) ?></span>
                        </p>
                        
                        <?php if($product['stock'] > 0): ?>
                            <?php if($product['stock'] <= 10): ?>
                                <p class="text-warning small mb-2"><strong>⚠️ Limited Stock - Only <?= $product['stock'] ?> remaining!</strong></p>
                            <?php endif; ?>
                            <form action="add-to-cart.php" method="POST" class="product-form">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                                <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['image_url']) ?>">
                                <input type="hidden" name="quantity" class="qty-value" value="1">
                                <input type="hidden" name="max_stock" value="<?= $product['stock'] ?>">
                                
                                <button type="button" class="btn btn-primary w-100 add-cart-btn" onclick="showQuantity(this)">
                                    Add to Cart
                                </button>
                                
                                <div class="quantity-section" style="display: none;">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold small">Quantity</label>
                                        <div class="input-group input-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity(this)">-</button>
                                            <input type="text" class="form-control text-center qty-display" value="1" readonly>
                                            <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity(this)">+</button>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100 btn-sm">Confirm Add to Cart</button>
                                </div>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100" disabled>Out of Stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-muted text-center">No products available.</p>
        <?php endif; ?>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="feature-box" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#shippingModal">
                        <div class="feature-icon">🚚</div>
                        <h4>Free Shipping</h4>
                        <p class="text-muted">On orders over ₹2,000</p>
                        <small class="text-primary">Click for details →</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <div class="feature-icon">🔒</div>
                        <h4>Secure Payment</h4>
                        <p class="text-muted">100% secure transactions</p>
                        <small class="text-primary">Click for details →</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#returnsModal">
                        <div class="feature-icon">↩️</div>
                        <h4>Easy Returns</h4>
                        <p class="text-muted">30-day return policy</p>
                        <small class="text-primary">Click for details →</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shipping Modal -->
    <div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="shippingModalLabel">🚚 Free Shipping Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold mb-3">Enjoy Free Shipping on Orders Over ₹2,000!</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">✓ <strong>Free Delivery:</strong> All orders above ₹2,000 qualify for free shipping across India</li>
                        <li class="mb-2">✓ <strong>Delivery Time:</strong> 3-7 business days depending on your location</li>
                        <li class="mb-2">✓ <strong>Packaging:</strong> Secure and eco-friendly packaging</li>
                        <li class="mb-2">✓ <strong>Coverage:</strong> We deliver to all pin codes across India</li>
                    </ul>
                    <div class="alert alert-info mt-3">
                        <small><strong>Note:</strong> For orders below ₹2,000, a nominal shipping charge of ₹99 applies.</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it!</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="paymentModalLabel">🔒 Secure Payment Options</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold mb-3">Payment Method</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">✓ <strong>Cash on Delivery (COD):</strong> Pay when you receive your order</li>
                    </ul>
                    <div class="alert alert-info mt-3">
                        <small><strong>Note:</strong> We currently accept Cash on Delivery only. Pay conveniently when your order arrives at your doorstep.</small>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it!</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Returns Modal -->
    <div class="modal fade" id="returnsModal" tabindex="-1" aria-labelledby="returnsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="returnsModalLabel">↩️ Easy Returns & Exchange</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold mb-3">Hassle-Free 30-Day Return Policy</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">✓ <strong>30-Day Window:</strong> Return or exchange within 30 days of delivery</li>
                        <li class="mb-2">✓ <strong>Free Return Pickup:</strong> We'll pick up the product from your doorstep</li>
                        <li class="mb-2">✓ <strong>Full Refund:</strong> Get 100% refund if product is defective or damaged</li>
                        <li class="mb-2">✓ <strong>Easy Exchange:</strong> Exchange for different size, color, or model</li>
                        <li class="mb-2">✓ <strong>Quality Check:</strong> All products undergo quality inspection before dispatch</li>
                    </ul>
                    <div class="alert alert-warning mt-3">
                        <small><strong>Conditions:</strong> Product must be unused, in original packaging with all tags intact. Prescription eyewear may have different return terms.</small>
                    </div>
                    <div class="mt-3">
                        <p class="mb-1"><strong>Need Help?</strong></p>
                        <p class="text-muted small mb-0">Contact us: bhosaleopticians@gmail.com | +91 9960815363</p>
                    </div>
        </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Got it!</button>
                </div>
            </div>
        </div>
    </div>

<?php
// Include footer
include 'includes/footer.php';
?>
