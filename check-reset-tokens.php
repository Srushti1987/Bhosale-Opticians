<?php
require_once 'config.php';

$conn = getDBConnection();

// Check if password_resets table exists
$check_table = "SHOW TABLES LIKE 'password_resets'";
$result = $conn->query($check_table);

echo "<h2>Password Reset Debug</h2>";

if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ password_resets table exists</p>";
    
    // Check tokens in table
    $sql = "SELECT * FROM password_resets ORDER BY created_at DESC LIMIT 5";
    $result = $conn->query($sql);
    
    echo "<h3>Recent Reset Tokens:</h3>";
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Email</th><th>Token (first 20 chars)</th><th>Created</th><th>Expires</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . substr($row['token'], 0, 20) . "...</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "<td>" . $row['expires_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>✗ No tokens found in database</p>";
        echo "<p>This means tokens are not being inserted when you request password reset.</p>";
    }
} else {
    echo "<p style='color: red;'>✗ password_resets table does NOT exist!</p>";
    echo "<p>Creating table now...</p>";
    
    $create_table = "CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        token VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP NOT NULL
    )";
    
    if ($conn->query($create_table)) {
        echo "<p style='color: green;'>✓ Table created successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create table: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<br><br>
<a href="forgot-password.php">Go to Forgot Password</a>
