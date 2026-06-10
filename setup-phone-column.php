<?php
/**
 * ONE-CLICK DATABASE UPDATE
 * This script adds the phone column to users table
 * Run once: http://localhost/project/setup-phone-column.php
 */

require_once 'config.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Update - Add Phone Column</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #17a2b8;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin: 10px 5px;
            cursor: pointer;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #218838;
        }
        .step {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .checkmark {
            color: #28a745;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Update Tool</h1>
        <p>This tool will add the <strong>phone</strong> column to your users table and update admin credentials.</p>

        <?php
        $conn = getDBConnection();
        $updates_made = false;
        $errors = [];

        // Step 1: Check if phone column exists
        echo "<div class='step'>";
        echo "<h3>Step 1: Checking phone column...</h3>";
        $column_check = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
        
        if ($column_check->num_rows == 0) {
            echo "<p>Phone column not found. Adding it now...</p>";
            $add_column = "ALTER TABLE users ADD COLUMN phone VARCHAR(15) DEFAULT NULL";
            
            if ($conn->query($add_column)) {
                echo "<div class='success'><span class='checkmark'>✓</span> Phone column added successfully!</div>";
                $updates_made = true;
            } else {
                $error_msg = "Error adding phone column: " . $conn->error;
                echo "<div class='error'>✗ $error_msg</div>";
                $errors[] = $error_msg;
            }
        } else {
            echo "<div class='info'><span class='checkmark'>✓</span> Phone column already exists.</div>";
        }
        echo "</div>";

        // Step 2: Update admin phone number
        echo "<div class='step'>";
        echo "<h3>Step 2: Updating admin phone number...</h3>";
        
        $update_admin = "UPDATE users SET phone = '9960815363' WHERE role = 'admin' AND (phone IS NULL OR phone = '')";
        
        if ($conn->query($update_admin)) {
            $affected = $conn->affected_rows;
            if ($affected > 0) {
                echo "<div class='success'><span class='checkmark'>✓</span> Admin phone number updated! ($affected row(s) affected)</div>";
                $updates_made = true;
            } else {
                echo "<div class='info'><span class='checkmark'>✓</span> Admin phone number already set.</div>";
            }
        } else {
            $error_msg = "Error updating admin: " . $conn->error;
            echo "<div class='error'>✗ $error_msg</div>";
            $errors[] = $error_msg;
        }
        echo "</div>";

        // Step 3: Show current admin details
        echo "<div class='step'>";
        echo "<h3>Step 3: Current Admin Details</h3>";
        $admin_query = $conn->query("SELECT id, name, email, phone, role FROM users WHERE role = 'admin' LIMIT 1");
        
        if ($admin_query && $admin_query->num_rows > 0) {
            $admin = $admin_query->fetch_assoc();
            echo "<table style='width:100%; border-collapse: collapse;'>";
            echo "<tr style='background:#f8f9fa;'><th style='padding:10px; border:1px solid #ddd; text-align:left;'>Field</th><th style='padding:10px; border:1px solid #ddd; text-align:left;'>Value</th></tr>";
            echo "<tr><td style='padding:10px; border:1px solid #ddd;'><strong>Name</strong></td><td style='padding:10px; border:1px solid #ddd;'>" . htmlspecialchars($admin['name']) . "</td></tr>";
            echo "<tr><td style='padding:10px; border:1px solid #ddd;'><strong>Email</strong></td><td style='padding:10px; border:1px solid #ddd;'>" . htmlspecialchars($admin['email']) . "</td></tr>";
            echo "<tr><td style='padding:10px; border:1px solid #ddd;'><strong>Phone</strong></td><td style='padding:10px; border:1px solid #ddd;'>" . ($admin['phone'] ? htmlspecialchars($admin['phone']) : '<em style=\"color:#999;\">Not set</em>') . "</td></tr>";
            echo "<tr><td style='padding:10px; border:1px solid #ddd;'><strong>Role</strong></td><td style='padding:10px; border:1px solid #ddd;'>" . htmlspecialchars($admin['role']) . "</td></tr>";
            echo "</table>";
        }
        echo "</div>";

        $conn->close();

        // Final Summary
        echo "<div style='margin-top: 30px; padding: 20px; background: #e7f3ff; border-radius: 5px;'>";
        echo "<h2 style='margin-top:0;'>✅ Update Complete!</h2>";
        
        if (count($errors) > 0) {
            echo "<div class='error'>";
            echo "<strong>Some errors occurred:</strong><ul>";
            foreach ($errors as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul></div>";
        } else {
            echo "<div class='success'>";
            echo "<strong>All updates completed successfully!</strong><br>";
            echo "Your database is now ready with phone number support.";
            echo "</div>";
        }

        echo "<h3>Admin Login Credentials:</h3>";
        echo "<ul style='font-size: 16px; line-height: 1.8;'>";
        echo "<li><strong>Email:</strong> admin@sunray.com</li>";
        echo "<li><strong>Phone:</strong> 9960815363</li>";
        echo "<li><strong>Password:</strong> password123</li>";
        echo "</ul>";

        echo "<h3>What's Next?</h3>";
        echo "<ol style='line-height: 1.8;'>";
        echo "<li>Delete this file (setup-phone-column.php) for security</li>";
        echo "<li>Try registering a new user with phone number</li>";
        echo "<li>Login as admin and manage your store</li>";
        echo "</ol>";

        echo "<div style='margin-top: 20px;'>";
        echo "<a href='register.php' class='btn btn-success'>Go to Registration</a>";
        echo "<a href='login.php' class='btn'>Go to Login</a>";
        echo "<a href='index_updated.php' class='btn'>Go to Home</a>";
        echo "</div>";
        echo "</div>";
        ?>

        <div style='margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;'>
            <strong>⚠️ Security Note:</strong> Please delete this file (setup-phone-column.php) after running it to prevent unauthorized access.
        </div>
    </div>
</body>
</html>
