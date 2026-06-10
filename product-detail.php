<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$conn = getDBConnection();
$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM products WHERE id = $id");

if ($result->num_rows == 0) {
    header('Location: products.php');
    exit();
}
$product = $result->fetch_assoc();

// Related products (same gender, exclude current)
$gender = $conn->real_escape_string($product['gender']);
$related = $conn->query("SELECT * FROM products WHERE gender = '$gender' AND id != $id LIMIT 4");
$related_products = [];
while ($row = $related->fetch_assoc()) $related_products[] = $row;

$conn->close();

$page_title = htmlspecialchars($product['name']) . " - Bhosale Opticians";
$base_url = "";
include 'includes/header.php';
?>

<section class="container my-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index_updated.php">Home</a></li>
            <li class="breadcrumb-item"><a href="products.php">Products</a></li>
            <li class="breadcrumb-item"><a href="products.php?gender=<?= urlencode($product['gender']) ?>"><?= htmlspecialchars($product['gender']) ?></a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <!-- Product Image -->
        <div class="col-md-5">
            <div class="position-relative">
                <?php if($product['on_sale']): ?>
                <span class="badge bg-danger position-absolute top-0 start-0 m-2 fs-6">SALE</span>
                <?php endif; ?>
                <?php if(!empty($product['badge'])): ?>
                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2"><?= htmlspecialchars($product['badge']) ?></span>
                <?php endif; ?>
                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="img-fluid rounded shadow"
                     style="width:100%; height:400px; object-fit:cover;">
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-md-7">
            <h1 class="fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="text-muted mb-3">
                <span class="badge bg-secondary me-1"><?= htmlspecialchars($product['category']) ?></span>
                <span class="badge bg-info text-dark"><?= htmlspecialchars($product['gender']) ?></span>
            </p>

            <!-- Price -->
            <div class="mb-4">
                <?php if($product['on_sale'] && $product['discount_percent'] > 0):
                    $original = $product['price'] / (1 - ($product['discount_percent'] / 100));
                ?>
                <span class="badge bg-danger me-2"><?= $product['discount_percent'] ?>% OFF</span>
                <del class="text-muted fs-5 me-2">₹<?= number_format($original, 2) ?></del>
                <?php endif; ?>
                <span class="fw-bold fs-2 text-brown">₹<?= number_format($product['price'], 2) ?></span>
            </div>

            <!-- Description -->
            <p class="mb-4"><?= htmlspecialchars($product['description']) ?></p>

            <!-- Specs -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm">
                    <?php if(!empty($product['brand'])): ?>
                    <tr><th width="35%">Brand</th><td><?= htmlspecialchars($product['brand']) ?></td></tr>
                    <?php endif; ?>
                    <?php if(!empty($product['model'])): ?>
                    <tr><th>Model</th><td><?= htmlspecialchars($product['model']) ?></td></tr>
                    <?php endif; ?>
                    <?php if(!empty($product['frame_material'])): ?>
                    <tr><th>Frame Material</th><td><?= htmlspecialchars($product['frame_material']) ?></td></tr>
                    <?php endif; ?>
                    <?php if(!empty($product['lens_type'])): ?>
                    <tr><th>Lens Type</th><td><?= htmlspecialchars($product['lens_type']) ?></td></tr>
                    <?php endif; ?>
                    <tr><th>Category</th><td><?= htmlspecialchars($product['category']) ?></td></tr>
                    <tr><th>Gender</th><td><?= htmlspecialchars($product['gender']) ?></td></tr>
                </table>
            </div>

            <!-- Stock & Add to Cart -->
            <?php if($product['stock'] > 0): ?>
                <?php if($product['stock'] <= 10): ?>
                <div class="alert alert-warning py-2">⚠️ Only <?= $product['stock'] ?> left in stock!</div>
                <?php else: ?>
                <p class="text-success mb-3">✓ In Stock</p>
                <?php endif; ?>

                <form action="add-to-cart.php" method="POST" class="d-flex gap-3 align-items-center">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                    <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['image_url']) ?>">
                    <input type="hidden" name="max_stock" value="<?= $product['stock'] ?>">
                    <div class="input-group" style="width:130px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="let i=this.nextElementSibling; if(i.value>1) i.value--;">-</button>
                        <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button type="button" class="btn btn-outline-secondary" onclick="let i=this.previousElementSibling; if(i.value<<?= $product['stock'] ?>) i.value++;">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg px-5">🛒 Add to Cart</button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
            <?php endif; ?>

            <div class="mt-3">
                <a href="products.php?gender=<?= urlencode($product['gender']) ?>" class="btn btn-outline-secondary">← Back to Products</a>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if(!empty($related_products)): ?>
    <hr class="my-5">
    <h3 class="fw-bold mb-4">Related Products</h3>
    <div class="row g-4">
        <?php foreach($related_products as $rp): ?>
        <div class="col-md-3">
            <div class="product-card h-100">
                <?php if($rp['on_sale']): ?><span class="sale-badge">SALE</span><?php endif; ?>
                <div class="product-image">
                    <img src="<?= htmlspecialchars($rp['image_url']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>">
                </div>
                <div class="product-body">
                    <h6 class="product-title"><?= htmlspecialchars($rp['name']) ?></h6>
                    <p class="product-price fw-bold text-brown">₹<?= number_format($rp['price'], 2) ?></p>
                    <a href="product-detail.php?id=<?= $rp['id'] ?>" class="btn btn-outline-primary btn-sm w-100">View Details</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
