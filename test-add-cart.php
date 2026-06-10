<?php
session_start();

echo "<h2>Cart Debug Test</h2>";

// Show current cart contents
echo "<h3>Current Cart Contents:</h3>";
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo "<pre>";
    print_r($_SESSION['cart']);
    echo "</pre>";
    echo "<p>Total items in cart: " . count($_SESSION['cart']) . "</p>";
} else {
    echo "<p>Cart is empty</p>";
}

// Test adding an item
if (isset($_GET['test'])) {
    $_SESSION['cart'][] = [
        'id' => 1,
        'name' => 'Test Product',
        'price' => 100.00,
        'image' => 'test.jpg',
        'quantity' => 1
    ];
    echo "<p style='color: green;'>Test item added!</p>";
    echo "<a href='test-add-cart.php'>Refresh to see cart</a>";
} else {
    echo "<a href='test-add-cart.php?test=1'>Click to add test item</a>";
}

echo "<hr>";
echo "<a href='cart-session.php'>View Cart Page</a> | ";
echo "<a href='index_updated.php'>Back to Home</a>";
?>
