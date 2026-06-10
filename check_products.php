<?php
require_once 'config.php';

echo "<h2>Product Database Check</h2>";

$conn = getDBConnection();

// Check total products
$result = $conn->query("SELECT COUNT(*) as total FROM products");
$row = $result->fetch_assoc();
echo "<p><strong>Total products:</strong> " . $row['total'] . "</p>";

// Check products by gender
echo "<h3>Products by Gender:</h3>";
$result = $conn->query("SELECT gender, COUNT(*) as count FROM products GROUP BY gender");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Gender</th><th>Count</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . htmlspecialchars($row['gender']) . "</td><td>" . $row['count'] . "</td></tr>";
}
echo "</table>";

// Show sample products
echo "<h3>Sample Products (First 10):</h3>";
$result = $conn->query("SELECT id, name, gender, category FROM products LIMIT 10");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Gender</th><th>Category</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
    echo "<td>" . htmlspecialchars($row['category']) . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();

echo "<hr>";
echo "<h3>Test Filtering:</h3>";
echo "<p>Click these links to test if filtering works:</p>";
echo "<ul>";
echo "<li><a href='products.php'>All Products</a></li>";
echo "<li><a href='products.php?gender=Men'>Men's Products</a></li>";
echo "<li><a href='products.php?gender=Women'>Women's Products</a></li>";
echo "<li><a href='products.php?gender=Kids'>Kids Products</a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Note:</strong> If all links show the same products, your database products don't have gender values set correctly.</p>";
echo "<p>Gender values must be exactly: 'Men', 'Women', or 'Kids' (case-sensitive)</p>";
?>
