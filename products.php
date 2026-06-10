<?php
require_once '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

$success = '';
$error = '';

// Delete product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn = getDBConnection();
    $sql = "DELETE FROM products WHERE id = $id";
    if ($conn->query($sql)) {
        $success = 'Product deleted successfully!';
    } else {
        $error = 'Failed to delete product.';
    }
    $conn->close();
}

// Add product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $conn = getDBConnection();
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $on_sale = isset($_POST['on_sale']) ? 1 : 0;
    $stock = intval($_POST['stock']);
    
    $sql = "INSERT INTO products (name, description, price, category, gender, image_url, on_sale, stock) 
            VALUES ('$name', '$description', $price, '$category', '$gender', '$image_url', $on_sale, $stock)";
    
    if ($conn->query($sql)) {
        $success = 'Product added successfully!';
    } else {
        $error = 'Failed to add product.';
    }
    $conn->close();
}

// Fetch all products
$conn = getDBConnection();
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Count low stock and out of stock products
$low_stock_count = 0;
$out_of_stock_count = 0;
foreach($products as $product) {
    if($product['stock'] == 0) {
        $out_of_stock_count++;
    } elseif($product['stock'] <= 10) {
        $low_stock_count++;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    
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
            <h2>Manage Products</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                + Add New Product
            </button>
        </div>

        <?php if($low_stock_count > 0 || $out_of_stock_count > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <strong>⚠️ Stock Alert!</strong>
            <?php if($out_of_stock_count > 0): ?>
                <span class="badge bg-danger"><?= $out_of_stock_count ?></span> product(s) are out of stock.
            <?php endif; ?>
            <?php if($low_stock_count > 0): ?>
                <span class="badge bg-warning text-dark"><?= $low_stock_count ?></span> product(s) have low stock (≤10 units).
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

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

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Sale</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($product['category']) ?> • <?= htmlspecialchars($product['gender']) ?></td>
                                <td class="text-brown fw-bold">₹<?= number_format($product['price'], 2) ?></td>
                                <td>
                                    <?php if($product['stock'] == 0): ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php elseif($product['stock'] <= 10): ?>
                                        <span class="badge bg-warning text-dark"><?= $product['stock'] ?> (Low Stock!)</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $product['stock'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($product['on_sale']): ?>
                                    <span class="badge bg-danger">On Sale</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="?delete=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control" required>
                                    <option value="Sunglasses">Sunglasses</option>
                                    <option value="Eyeglasses">Eyeglasses</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-control" required>
                                <option value="Men">👨 Men</option>
                                <option value="Women">👩 Women</option>
                                <option value="Kids">👶 Kids</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Price (₹)</label>
                                <input type="number" name="price" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">On Sale</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="on_sale" class="form-check-input" id="onSale">
                                    <label class="form-check-label" for="onSale">Yes</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="url" name="image_url" class="form-control" required>
                            <small class="text-muted">Enter the full URL of the product image</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
