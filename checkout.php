<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?role=user');
    exit();
}

$error = '';
$success = '';

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $payment_method = $_POST['payment_method'];
    
    // Get cart from localStorage (passed via hidden field)
    $cart_json = $_POST['cart_data'];
    $cart = json_decode($cart_json, true);
    
    if (empty($cart)) {
        $error = 'Your cart is empty!';
    } else {
        $conn = getDBConnection();
        $user_id = $_SESSION['user_id'];
        
        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            $price = floatval(str_replace(['₹', ','], '', $item['price']));
            $total += $price * $item['quantity'];
        }
        
        // Create shipping address
        $shipping_address = "$address, $city, $state - $pincode\nPhone: $phone";
        $shipping_address = $conn->real_escape_string($shipping_address);
        
        // Insert order
        $sql = "INSERT INTO orders (user_id, total_amount, status, shipping_address) 
                VALUES ($user_id, $total, 'pending', '$shipping_address')";
        
        if ($conn->query($sql)) {
            $order_id = $conn->insert_id;
            
            // Insert order items
            foreach ($cart as $item) {
                $product_id = intval($item['id']);
                $quantity = intval($item['quantity']);
                $price = floatval(str_replace(['₹', ','], '', $item['price']));
                
                $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                            VALUES ($order_id, $product_id, $quantity, $price)";
                $conn->query($item_sql);
            }
            
            $conn->close();
            
            // Redirect to bill page
            header("Location: bill.php?order_id=$order_id");
            exit();
        } else {
            $error = 'Failed to place order. Please try again.';
        }
        
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - SUNRAY</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="sticky-top bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg container">
            <div class="container-fluid px-3">
                <a class="navbar-brand fw-bold fs-4" href="index_updated.php">
                    <span class="text-brown">SUN</span>RAY
                </a>
            </div>
        </nav>
    </header>

    <section class="container my-5">
        <h2 class="mb-4">Checkout</h2>

        <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="checkoutForm">
                            <input type="hidden" name="cart_data" id="cartData">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user_email']) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+91 9960815363" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address *</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="House No, Street, Area" required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">City *</label>
                                    <input type="text" name="city" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">State *</label>
                                    <input type="text" name="state" class="form-control" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Pincode *</label>
                                    <input type="text" name="pincode" class="form-control" pattern="[0-9]{6}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Payment Method *</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                                    <label class="form-check-label" for="cod">
                                        Cash on Delivery (COD)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" value="online" id="online">
                                    <label class="form-check-label" for="online">
                                        Online Payment (UPI/Card)
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">Place Order</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div id="orderSummary"></div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">₹0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">FREE</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (18% GST):</span>
                            <span id="tax">₹0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="total" class="text-brown fs-4">₹0.00</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load cart and display summary
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        if (cart.length === 0) {
            window.location.href = 'cart.php';
        }
        
        let subtotal = 0;
        let summaryHTML = '';
        
        cart.forEach(item => {
            const price = parseFloat(item.price.replace('₹', '').replace(',', ''));
            const itemTotal = price * item.quantity;
            subtotal += itemTotal;
            
            summaryHTML += `
                <div class="d-flex justify-content-between mb-2">
                    <span>${item.name} x ${item.quantity}</span>
                    <span>₹${itemTotal.toFixed(2)}</span>
                </div>
            `;
        });
        
        const tax = subtotal * 0.18;
        const total = subtotal + tax;
        
        document.getElementById('orderSummary').innerHTML = summaryHTML;
        document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('tax').textContent = '₹' + tax.toFixed(2);
        document.getElementById('total').textContent = '₹' + total.toFixed(2);
        
        // Set cart data in hidden field
        document.getElementById('cartData').value = JSON.stringify(cart);
    </script>
</body>
</html>
