<?php
require_once 'config.php';

echo "<h2>Setting up Feedback System</h2>";

$conn = getDBConnection();

// Create feedback table
$sql = "CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT DEFAULT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    feedback_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✅ Feedback table created successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Error creating feedback table: " . $conn->error . "</p>";
}

// Add some sample feedback data
$sample_sql = "INSERT IGNORE INTO feedback (user_id, order_id, rating, feedback_text) VALUES
(1, NULL, 5, 'Excellent service and quality products! Very satisfied with my purchase.'),
(1, NULL, 4, 'Good collection of eyewear. Fast delivery and professional packaging.')";

if ($conn->query($sample_sql) === TRUE) {
    echo "<p style='color: green;'>✅ Sample feedback data added!</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Sample data might already exist: " . $conn->error . "</p>";
}

$conn->close();

echo "<h3>Feedback System Setup Complete!</h3>";
echo "<p><strong>Features added:</strong></p>";
echo "<ul>";
echo "<li>✅ Expandable feedback form on checkout page</li>";
echo "<li>✅ Star rating system (1-5 stars)</li>";
echo "<li>✅ Text feedback collection</li>";
echo "<li>✅ Admin feedback dashboard at <a href='admin/feedback.php'>admin/feedback.php</a></li>";
echo "<li>✅ Feedback statistics and analytics</li>";
echo "<li>✅ Smooth animations and user-friendly interface</li>";
echo "</ul>";

echo "<p><a href='checkout-session.php'>Test Checkout Page</a> | <a href='admin/feedback.php'>View Admin Feedback</a></p>";
?>