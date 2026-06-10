<?php
/**
 * Admin Credentials Update Script
 * Run this file once to update admin email and phone number
 * Access: http://localhost/project/update-admin-credentials.php
 */

require_once 'config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Update Admin Credentials</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🔧 Admin Credentials Update</h1>";

$conn = getDBConnection();

// Check if phone column exists
$check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
if ($check_column->num_rows == 0) {
    echo "<div class='info'>Adding 'phone' column to users table...</div>";
    $add_column = "ALTER TABLE users ADD COLUMN phone VARCHAR(15) DEFAULT NULL";
    if ($conn->query($add_column)) {
        echo "<div class='success'>✓ Phone column added successfully!</div>";
    } else {
        echo "<div class='error'>✗ Error adding phone column: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='info'>✓ Phone column already exists.</div>";
}

// Show current admin details
echo "<h2>Current Admin Details:</h2>";
$current = $conn->query("SELECT id, name, email, phone, role FROM users WHERE role = 'admin'");
if ($current->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th></tr>";
    while ($row = $current->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . ($row['phone'] ? htmlspecialchars($row['phone']) : '<em>Not set</em>') . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

-- Update admin credentials
echo "<h2>Updating Admin Credentials...</h2>";

$update_sql = "UPDATE users 
               SET phone = '9960815363' 
               WHERE role = 'admin'";

if ($conn->query($update_sql)) {
    $affected = $conn->affected_rows;
    if ($affected > 0) {
        echo "<div class='success'>✓ Admin phone number updated successfully! ($affected row(s) affected)</div>";
    } else {
        echo "<div class='info'>ℹ No changes needed - phone number already up to date.</div>";
    }
} else {
    echo "<div class='error'>✗ Error updating admin: " . $conn->error . "</div>";
}

// Show updated admin details
echo "<h2>Updated Admin Details:</h2>";
$updated = $conn->query("SELECT id, name, email, phone, role FROM users WHERE role = 'admin'");
if ($updated->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th></tr>";
    while ($row = $updated->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . ($row['phone'] ? htmlspecialchars($row['phone']) : '<em>Not set</em>') . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();

echo "<div class='success'>
    <h3>✓ Update Complete!</h3>
    <p><strong>Admin Login Credentials:</strong></p>
    <ul>
        <li><strong>Email:</strong> admin@sunray.com</li>
        <li><strong>Phone:</strong> 9960815363</li>
        <li><strong>Password:</strong> password123</li>
    </ul>
</div>";

echo "<div class='info'>
    <p><strong>⚠️ Security Note:</strong> Please delete this file (update-admin-credentials.php) after running it for security reasons.</p>
</div>";

echo "<p><a href='login.php' class='btn'>Go to Login Page</a></p>";

echo "</body></html>";
?>
