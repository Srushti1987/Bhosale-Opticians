<?php
session_start();
require_once 'config.php';

// Set page variables
$page_title = "Products - Bhosale Opticians";
$active_page = "products";
$base_url = "";

// Fetch all products
$conn = getDBConnection();
$where = "1=1";

if(isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $where .= " AND category = '$category'";
}

if(isset($_GET['gender']) && !empty($_GET['gender'])) {
    $gender = $conn->real_escape_string($_GET['gender']);
    $where .= " AND gender = '$gender'";
}

if(isset($_GET['sale'])) {
    $where .= " AND on_sale = 1";
}

// Search
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (name LIKE '%$search%' OR description LIKE '%$search%' OR category LIKE '%$search%')";
}

// Price filter
if(isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $where .= " AND price >= " . floatval($_GET['min_price']);
}
if(isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $where .= " AND price <= " . floatval($_GET['max_price']);
}

// Sort
$sort = "created_at DESC";
if(isset($_GET['sort'])) {
    switch($_GET['sort']) {
        case 'price_asc':  $sort = "price ASC"; break;
        case 'price_desc': $sort = "price DESC"; break;
        case 'name_asc':   $sort = "name ASC"; break;
        case 'newest':     $sort = "created_at DESC"; break;
    }
}

$sql = "SELECT * FROM products WHERE $where ORDER BY $sort";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();

// Include header
include 'includes/header.php';
?>

    <!-- Page Header -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">
                <?php 
                if(isset($_GET['gender'])) {
                    echo htmlspecialchars($_GET['gender']) . "'s Products";
                } elseif(isset($_GET['sale'])) {
                    echo "Products on Sale";
                } else {
                    echo "Our Products";
                }
                ?>
            </h1>
            <p class="lead text-muted">
                <?php 
                echo "Showing " . count($products) . " product(s)";
                if(isset($_GET['gender'])) {
                    echo " for " . htmlspecialchars($_GET['gender']);
                }
                ?>
            </p>
            <?php if(isset($_GET['added'])): ?>
            <div class="alert alert-success mt-3">✓ Product added to cart! <a href="cart-session.php">View Cart</a></div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Filters & Search -->
    <section class="container my-4">
        <form method="GET" action="products.php" id="filterForm">
            <!-- Search Bar -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="btn btn-primary" type="submit">🔍 Search</button>
                        <?php if(!empty($_GET['search'])): ?>
                        <a href="products.php" class="btn btn-outline-secondary">✕ Clear</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Sort: Newest</option>
                        <option value="price_asc" <?= ($_GET['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= ($_GET['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="name_asc" <?= ($_GET['sort'] ?? '') == 'name_asc' ? 'selected' : '' ?>>Name: A to Z</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <input type="number" name="min_price" class="form-control" placeholder="Min ₹" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                    <input type="number" name="max_price" class="form-control" placeholder="Max ₹" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                    <button class="btn btn-outline-primary" type="submit">Go</button>
                </div>
            </div>
            <!-- Gender Filter Buttons -->
            <div class="btn-group mb-3 flex-wrap" role="group">
                <a href="products.php<?= isset($_GET['search']) ? '?search='.urlencode($_GET['search']) : '' ?>" class="btn btn-outline-primary <?= !isset($_GET['gender']) && !isset($_GET['sale']) ? 'active' : '' ?>">All</a>
                <a href="products.php?gender=Men" class="btn btn-outline-primary <?= ($_GET['gender'] ?? '') == 'Men' ? 'active' : '' ?>">👨 Men</a>
                <a href="products.php?gender=Women" class="btn btn-outline-primary <?= ($_GET['gender'] ?? '') == 'Women' ? 'active' : '' ?>">👩 Women</a>
                <a href="products.php?gender=Kids" class="btn btn-outline-primary <?= ($_GET['gender'] ?? '') == 'Kids' ? 'active' : '' ?>">👶 Kids</a>
                <a href="products.php?sale=1" class="btn btn-outline-danger <?= isset($_GET['sale']) ? 'active' : '' ?>">🔥 On Sale</a>
            </div>
            <?php if(isset($_GET['gender'])): ?>
            <input type="hidden" name="gender" value="<?= htmlspecialchars($_GET['gender']) ?>">
            <?php endif; ?>
        </form>
    </section>

    <!-- Products Grid -->
    <section class="container my-5">
        <div class="row g-4">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                <div class="col-md-6 col-lg-4 col-xl-3">
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
                            <h5 class="product-title">
                                <a href="product-detail.php?id=<?= $product['id'] ?>" class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h5>
                            <p class="product-category text-muted mb-2"><?= htmlspecialchars($product['category']) ?> · <?= htmlspecialchars($product['gender']) ?></p>
                            <p class="text-muted small mb-3"><?= htmlspecialchars(substr($product['description'], 0, 60)) ?>...</p>
                            <p class="product-price mb-3">
                                <?php if($product['on_sale'] && $product['discount_percent'] > 0): ?>
                                <?php 
                                    $discount = $product['discount_percent'];
                                    $original_price = $product['price'] / (1 - ($discount / 100));
                                ?>
                                <span class="badge bg-danger mb-2"><?= $discount ?>% OFF</span><br>
                                <del class="text-danger me-2">₹<?= number_format($original_price, 2) ?></del>
                                <?php endif; ?>
                                <span class="fw-bold fs-4 text-brown">₹<?= number_format($product['price'], 2) ?></span>
                            </p>
                            
                            <?php if($product['stock'] > 0): ?>
                                <?php if($product['stock'] <= 10): ?>
                                    <p class="text-warning small mb-2">
                                        <strong>⚠️ Limited Stock - Only <?= $product['stock'] ?> remaining!</strong>
                                    </p>
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
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No products found.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php
// Include footer
include 'includes/footer.php';
?>
