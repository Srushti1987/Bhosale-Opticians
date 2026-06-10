<?php
// Razorpay payment verification & order creation
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

$razorpay_payment_id = $data['razorpay_payment_id'] ?? '';
$razorpay_order_id   = $data['razorpay_order_id'] ?? '';
$razorpay_signature  = $data['razorpay_signature'] ?? '';
$order_data          = $data['order_data'] ?? [];

// Verify signature (skip if using test placeholder keys)
$skip_verify = (RAZORPAY_KEY_SECRET === 'YOUR_KEY_SECRET');

if (!$skip_verify) {
    $generated_signature = hash_hmac(
        'sha256',
        $razorpay_order_id . '|' . $razorpay_payment_id,
        RAZORPAY_KEY_SECRET
    );

    if ($generated_signature !== $razorpay_signature) {
        echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
        exit();
    }
}

// Payment verified — save order
$conn = getDBConnection();
$user_id = $_SESSION['user_id'];
$cart    = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit();
}

// Calculate total
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
$tax   = $total * 0.18;
$total_with_tax = $total + $tax;

$shipping_address = $conn->real_escape_string($order_data['shipping_address'] ?? '');
$payment_id_esc   = $conn->real_escape_string($razorpay_payment_id);

// Insert order
$sql = "INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_id) 
        VALUES ($user_id, $total_with_tax, 'confirmed', '$shipping_address', '$payment_id_esc')";

if ($conn->query($sql)) {
    $order_id = $conn->insert_id;

    foreach ($cart as $item) {
        $product_id = intval($item['id']);
        $quantity   = intval($item['quantity']);
        $price      = floatval($item['price']);

        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, $quantity, $price)");
        $conn->query("UPDATE products SET stock = stock - $quantity WHERE id = $product_id");
    }

    $_SESSION['cart'] = [];
    $conn->close();

    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Failed to save order']);
}
?>
