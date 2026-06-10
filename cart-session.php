<?php
session_start();

// Set page variables
$page_title = "Shopping Cart - Bhosale Opticians";
$active_page = "cart";
$base_url = "";

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }
    header('Location: cart-session.php');
    exit();
}

$cart = $_SESSION['cart'];
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// Include header
include 'includes/header.php';
?>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">Shopping Cart</h1>
            <p class="lead text-muted">Review your items</p>
        </div>
    </section>

    <section class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <?php if (empty($cart)): ?>
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <h4 class="text-muted mb-4">Your cart is empty</h4>
                        <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                    </div>
                </div>
                <?php else: ?>
                    <?php foreach ($cart as $index => $item): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 mb-3 mb-md-0">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                         style="max-height: 100px; object-fit: cover;">
                                </div>
                                <div class="col-md-5 mb-3 mb-md-0">
                                    <h5 class="mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                                    <p class="text-muted small mb-0">Quantity: <?= $item['quantity'] ?></p>
                                </div>
                                <div class="col-md-3 mb-3 mb-md-0">
                                    <h5 class="text-brown mb-0">₹<?= number_format($item['price'], 2) ?></h5>
                                    <p class="text-muted small mb-0">Total: ₹<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                                </div>
                                <div class="col-md-2 text-end">
                                    <a href="?remove=<?= $index ?>" class="btn btn-danger btn-sm">Remove</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body">
                        <h4 class="mb-4">Order Summary</h4>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong class="text-brown fs-4">₹<?= number_format($subtotal, 2) ?></strong>
                        </div>
                        <?php if (!empty($cart)): ?>
                        <a href="checkout-session.php" class="btn btn-primary w-100 py-3">Proceed to Checkout</a>
                        <?php else: ?>
                        <button class="btn btn-secondary w-100 py-3" disabled>Cart is Empty</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer
include 'includes/footer.php';
?>
