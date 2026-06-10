<?php
session_start();

// Initialize cart in session if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = floatval($_POST['product_price']);
    $product_image = $_POST['product_image'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $max_stock = isset($_POST['max_stock']) ? intval($_POST['max_stock']) : 999;
    
    // Validate quantity
    if ($quantity < 1) $quantity = 1;
    if ($quantity > $max_stock) $quantity = $max_stock;
    
    // Check if product already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] += $quantity;
            if ($item['quantity'] > $max_stock) {
                $item['quantity'] = $max_stock;
            }
            $found = true;
            break;
        }
    }
    
    // Add new product if not found
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'image' => $product_image,
            'quantity' => $quantity
        ];
    }
    
    // Redirect back to products page
    header('Location: products.php?added=1');
    exit();
}
?>

