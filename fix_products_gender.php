<?php
require_once 'config.php';

echo "<h2>Fix Product Gender Values</h2>";

$conn = getDBConnection();

// First, check current state
$result = $conn->query("SELECT gender, COUNT(*) as count FROM products GROUP BY gender");
echo "<h3>Current State:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Gender</th><th>Count</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . htmlspecialchars($row['gender']) . "</td><td>" . $row['count'] . "</td></tr>";
}
echo "</table>";

// Get total count
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$total = $result->fetch_assoc()['total'];

if ($total < 20) {
    echo "<p style='color: red;'>⚠️ You only have $total products. You need to import more products!</p>";
    echo "<p><a href='import_products.php'>Click here to import 70 products</a></p>";
} else {
    // Update products to have correct gender distribution
    echo "<h3>Updating Gender Values...</h3>";
    
    // Get all product IDs
    $result = $conn->query("SELECT id FROM products ORDER BY id");
    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }
    
    $total_products = count($ids);
    $men_count = ceil($total_products * 0.4); // 40% Men
    $women_count = ceil($total_products * 0.4); // 40% Women
    // Rest will be Kids
    
    $updated = 0;
    
    // Update Men
    for ($i = 0; $i < $men_count && $i < $total_products; $i++) {
        $conn->query("UPDATE products SET gender = 'Men' WHERE id = " . $ids[$i]);
        $updated++;
    }
    
    // Update Women
    for ($i = $men_count; $i < ($men_count + $women_count) && $i < $total_products; $i++) {
        $conn->query("UPDATE products SET gender = 'Women' WHERE id = " . $ids[$i]);
        $updated++;
    }
    
    // Update Kids (remaining)
    for ($i = ($men_count + $women_count); $i < $total_products; $i++) {
        $conn->query("UPDATE products SET gender = 'Kids' WHERE id = " . $ids[$i]);
        $updated++;
    }
    
    echo "<p style='color: green;'>✓ Updated $updated products!</p>";
    
    // Show new state
    $result = $conn->query("SELECT gender, COUNT(*) as count FROM products GROUP BY gender");
    echo "<h3>New State:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Gender</th><th>Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($row['gender']) . "</td><td>" . $row['count'] . "</td></tr>";
    }
    echo "</table>";
}

$conn->close();

echo "<hr>";
echo "<h3>Test the pages:</h3>";
echo "<ul>";
echo "<li><a href='products.php'>All Products (should show mix of all genders)</a></li>";
echo "<li><a href='products.php?gender=Men'>Men's Products</a></li>";
echo "<li><a href='products.php?gender=Women'>Women's Products</a></li>";
echo "<li><a href='products.php?gender=Kids'>Kids Products</a></li>";
echo "</ul>";
?>
