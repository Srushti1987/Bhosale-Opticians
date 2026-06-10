<?php
require_once 'config.php';

echo "<h2>Import Products</h2>";

// Read the SQL file
$sql_file = 'add_more_products.sql';

if (!file_exists($sql_file)) {
    die("Error: $sql_file not found!");
}

$sql_content = file_get_contents($sql_file);

// Connect to database
$conn = getDBConnection();

// Execute the SQL
if ($conn->multi_query($sql_content)) {
    echo "<p style='color: green;'>✓ Products imported successfully!</p>";
    
    // Clear all results
    while ($conn->more_results()) {
        $conn->next_result();
    }
    
    // Count products
    $result = $conn->query("SELECT COUNT(*) as total FROM products");
    $row = $result->fetch_assoc();
    echo "<p>Total products in database: <strong>" . $row['total'] . "</strong></p>";
    
    // Count by gender
    $result = $conn->query("SELECT gender, COUNT(*) as count FROM products GROUP BY gender");
    echo "<h3>Products by Gender:</h3><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . $row['gender'] . ": " . $row['count'] . " products</li>";
    }
    echo "</ul>";
    
} else {
    echo "<p style='color: red;'>Error importing products: " . $conn->error . "</p>";
}

$conn->close();

echo "<hr>";
echo "<a href='products.php'>View All Products</a> | ";
echo "<a href='products.php?gender=Men'>View Men's Products</a> | ";
echo "<a href='products.php?gender=Women'>View Women's Products</a> | ";
echo "<a href='products.php?gender=Kids'>View Kids Products</a>";
?>
