<?php
session_start();
require_once 'config.php';

// Set page variables
$page_title = "Checkout - Bhosale Opticians";
$active_page = "checkout";
$base_url = "";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?role=user');
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart-session.php');
    exit();
}

$error = '';

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    
    $cart = $_SESSION['cart'];
    
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    
    // Validate stock availability
    $stock_error = false;
    foreach ($cart as $item) {
        $product_id = intval($item['id']);
        $quantity = intval($item['quantity']);
        
        $check_sql = "SELECT stock, name FROM products WHERE id = $product_id";
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            $product = $check_result->fetch_assoc();
            if ($product['stock'] < $quantity) {
                $error = "Sorry, only {$product['stock']} units of '{$product['name']}' are available in stock.";
                $stock_error = true;
                break;
            }
        }
    }
    
    if (!$stock_error) {
        // Calculate total
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Add 18% GST
        $tax = $total * 0.18;
        $total_with_tax = $total + $tax;
        
        // Create shipping address
        $shipping_address = "$address, $city, $state - $pincode\nPhone: $phone";
        $shipping_address = $conn->real_escape_string($shipping_address);
        
        // Insert order with COD
        $sql = "INSERT INTO orders (user_id, total_amount, status, shipping_address) 
                VALUES ($user_id, $total_with_tax, 'pending', '$shipping_address')";
        
        if ($conn->query($sql)) {
            $order_id = $conn->insert_id;
            
            // Insert order items and update stock
            foreach ($cart as $item) {
                $product_id = intval($item['id']);
                $quantity = intval($item['quantity']);
                $price = floatval($item['price']);
                
                // Insert order item
                $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                            VALUES ($order_id, $product_id, $quantity, $price)";
                $conn->query($item_sql);
                
                // Update product stock
                $update_stock_sql = "UPDATE products SET stock = stock - $quantity WHERE id = $product_id";
                $conn->query($update_stock_sql);
            }
            
            // Clear cart
            $_SESSION['cart'] = [];
            $conn->close();
            
            // Redirect to feedback form page
            header("Location: feedback-form.php?order_id=$order_id");
            exit();
        } else {
            $error = 'Failed to place order. Please try again.';
        }
    }
    
    $conn->close();
}

$cart = $_SESSION['cart'];
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.18;
$total = $subtotal + $tax;

// Include header
include 'includes/header.php';
?>

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
                        <form method="POST">
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
                                <label class="form-label fw-bold">Payment Method *</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card border-2 payment-option" id="cod-card" onclick="selectPayment('cod')" style="cursor:pointer; border-color:#0d6efd;">
                                            <div class="card-body text-center py-3">
                                                <div style="font-size:2rem;">💵</div>
                                                <h6 class="mb-1 fw-bold">Cash on Delivery</h6>
                                                <small class="text-muted">Pay when you receive</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-2 payment-option" id="online-card" onclick="selectPayment('online')" style="cursor:pointer; border-color:#dee2e6;">
                                            <div class="card-body text-center py-3">
                                                <div style="font-size:2rem;">💳</div>
                                                <h6 class="mb-1 fw-bold">Online Payment</h6>
                                                <small class="text-muted">UPI / Card / Net Banking</small>
                                                <?php if(RAZORPAY_KEY_ID === 'rzp_test_YOUR_KEY_ID'): ?>
                                                <br><span class="badge bg-warning text-dark mt-1">Setup Required</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="payment_method" id="payment_method" value="cod">
                            </div>

                            <button type="submit" id="placeOrderBtn" class="btn btn-primary btn-lg w-100">
                                Place Order (COD)
                            </button>
                            <button type="button" id="payOnlineBtn" class="btn btn-success btn-lg w-100" style="display:none;" onclick="initiateRazorpay()">
                                💳 Pay ₹<?= number_format($total, 2) ?> Online
                            </button>
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
                        <?php foreach ($cart as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></span>
                            <span>₹<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>₹<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">FREE</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (18% GST):</span>
                            <span>₹<?= number_format($tax, 2) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="text-brown fs-4">₹<?= number_format($total, 2) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer
include 'includes/footer.php';
?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function selectPayment(method) {
    document.getElementById('payment_method').value = method;

    if (method === 'online') {
        document.getElementById('cod-card').style.borderColor = '#dee2e6';
        document.getElementById('online-card').style.borderColor = '#198754';
        document.getElementById('placeOrderBtn').style.display = 'none';
        document.getElementById('payOnlineBtn').style.display = 'block';
    } else {
        document.getElementById('cod-card').style.borderColor = '#0d6efd';
        document.getElementById('online-card').style.borderColor = '#dee2e6';
        document.getElementById('placeOrderBtn').style.display = 'block';
        document.getElementById('payOnlineBtn').style.display = 'none';
    }
}

function initiateRazorpay() {
    // Check if keys are configured
    const keyId = '<?= RAZORPAY_KEY_ID ?>';
    if (keyId === 'rzp_test_YOUR_KEY_ID' || keyId === '') {
        alert('⚠️ Razorpay is not configured yet.\n\nTo enable online payment:\n1. Go to razorpay.com and sign up\n2. Get your Test API Keys\n3. Update config.php with your keys\n\nFor now, please use Cash on Delivery.');
        selectPayment('cod');
        return;
    }

    // Validate form first
    const form = document.querySelector('form');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const amount = <?= round($total * 100) ?>; // in paise

    const options = {
        key: '<?= RAZORPAY_KEY_ID ?>',
        amount: amount,
        currency: 'INR',
        name: 'Bhosale Opticians',
        description: 'Order Payment',
        image: '',
        handler: function(response) {
            // Payment successful — verify and save order
            const formData = new FormData(form);
            const address = `${formData.get('address')}, ${formData.get('city')}, ${formData.get('state')} - ${formData.get('pincode')}\nPhone: ${formData.get('phone')}`;

            fetch('verify-payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id:   response.razorpay_order_id || '',
                    razorpay_signature:  response.razorpay_signature || '',
                    order_data: { shipping_address: address }
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'feedback-form.php?order_id=' + data.order_id + '&payment=success';
                } else {
                    alert('Payment received but order saving failed. Contact support with Payment ID: ' + response.razorpay_payment_id);
                }
            });
        },
        prefill: {
            name:  '<?= htmlspecialchars($_SESSION['user_name']) ?>',
            email: '<?= htmlspecialchars($_SESSION['user_email']) ?>',
            contact: document.querySelector('[name="phone"]')?.value || ''
        },
        theme: { color: '#8B4513' },
        modal: {
            ondismiss: function() {
                console.log('Payment cancelled');
            }
        }
    };

    const rzp = new Razorpay(options);
    rzp.open();
}
</script>
