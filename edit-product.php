<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$success = '';
$error = '';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$conn = getDBConnection();
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: products.php');
    exit();
}

$product = $result->fetch_assoc();

// Update product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $on_sale = isset($_POST['on_sale']) ? 1 : 0;
    $stock = intval($_POST['stock']);
    
    $update_sql = "UPDATE products SET 
                   name = '$name', 
                   description = '$description', 
                   price = $price, 
                   category = '$category', 
                   image_url = '$image_url', 
                   on_sale = $on_sale, 
                   stock = $stock 
                   WHERE id = $product_id";
    
    if ($conn->query($update_sql)) {
        $success = 'Product updated successfully!';
        // Refresh product data
        $result = $conn->query($sql);
        $product = $result->fetch_assoc();
    } else {
        $error = 'Failed to update product.';
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="../index.php">
                    Bhosale Opticians <span class="badge bg-danger">Admin</span>
                </a>
                
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link active" href="products.php">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="appointments.php">Appointments</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <section class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Product</h2>
            <a href="products.php" class="btn btn-secondary">← Back to Products</a>
        </div>

        <?php if($success): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?= htmlspecialchars($product['name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category" class="form-control" required>
                                        <option value="Sunglasses" <?= $product['category'] == 'Sunglasses' ? 'selected' : '' ?>>Sunglasses</option>
                                        <option value="Eyeglasses" <?= $product['category'] == 'Eyeglasses' ? 'selected' : '' ?>>Eyeglasses</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (₹)</label>
                                    <input type="number" name="price" class="form-control" step="0.01" 
                                           value="<?= $product['price'] ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock Quantity</label>
                                    <input type="number" name="stock" class="form-control" 
                                           value="<?= $product['stock'] ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sale Status</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="on_sale" class="form-check-input" id="onSale" 
                                               <?= $product['on_sale'] ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="onSale">
                                            Mark as On Sale
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Image URL</label>
                                <input type="url" name="image_url" class="form-control" 
                                       value="<?= htmlspecialchars($product['image_url']) ?>" required>
                                <small class="text-muted">Enter the full URL of the product image</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">Update Product</button>
                                <a href="products.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Product Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="product-card">
                            <?php if($product['on_sale']): ?>
                            <span class="sale-badge">SALE</span>
                            <?php endif; ?>
                            
                            <div class="product-image">
                                <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                            </div>
                            
                            <div class="product-body">
                                <h5 class="product-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="product-category text-muted"><?= htmlspecialchars($product['category']) ?></p>
                                <p class="text-muted small"><?= htmlspecialchars(substr($product['description'], 0, 60)) ?>...</p>
                                <p class="product-price">
                                    <?php if($product['on_sale']): ?>
                                    <del class="text-danger">₹<?= number_format($product['price'] * 1.25, 2) ?></del>
                                    <?php endif; ?>
                                    <span class="fw-bold fs-4 text-brown">₹<?= number_format($product['price'], 2) ?></span>
                                </p>
                                <p class="text-muted small">Stock: <?= $product['stock'] ?> units</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="mb-3">Product Information</h6>
                        <p class="small mb-2"><strong>Product ID:</strong> #<?= $product['id'] ?></p>
                        <p class="small mb-2"><strong>Created:</strong> <?= date('M d, Y', strtotime($product['created_at'])) ?></p>
                        <p class="small mb-0"><strong>Status:</strong> 
                            <span class="badge bg-<?= $product['stock'] > 0 ? 'success' : 'danger' ?>">
                                <?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live preview update
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('input', function() {
                const name = document.querySelector('input[name="name"]').value;
                const price = document.querySelector('input[name="price"]').value;
                const category = document.querySelector('select[name="category"]').value;
                const description = document.querySelector('textarea[name="description"]').value;
                const imageUrl = document.querySelector('input[name="image_url"]').value;
                const onSale = document.querySelector('input[name="on_sale"]').checked;
                const stock = document.querySelector('input[name="stock"]').value;
                
                // Update preview
                document.querySelector('.product-title').textContent = name;
                document.querySelector('.product-category').textContent = category;
                document.querySelector('.product-body .text-muted.small').textContent = description.substring(0, 60) + '...';
                document.querySelector('.product-price .text-brown').textContent = '₹' + parseFloat(price).toFixed(2);
                document.querySelector('.product-image img').src = imageUrl;
                document.querySelector('.product-body p.text-muted.small:last-child').textContent = 'Stock: ' + stock + ' units';
                
                // Update sale badge
                const saleBadge = document.querySelector('.sale-badge');
                if (onSale && !saleBadge) {
                    const badge = document.createElement('span');
                    badge.className = 'sale-badge';
                    badge.textContent = 'SALE';
                    document.querySelector('.product-card').prepend(badge);
                } else if (!onSale && saleBadge) {
                    saleBadge.remove();
                }
            });
        });
    </script>
</body>
</html>
