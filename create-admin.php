<?php
require_once 'config.php';

// Create admin account
$name = "Admin User";
$email = "admin@sunray.com";
$password = "password123";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$conn = getDBConnection();

// Check if admin already exists
$check_sql = "SELECT * FROM users WHERE email = 'admin@sunray.com'";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    // Update existing admin
    $update_sql = "UPDATE users SET password = '$hashed_password', role = 'admin' WHERE email = 'admin@sunray.com'";
    if ($conn->query($update_sql)) {
        echo "<h2 style='color: green;'>✅ Admin account updated successfully!</h2>";
    } else {
        echo "<h2 style='color: red;'>❌ Failed to update admin account</h2>";
    }
} else {
    // Create new admin
    $insert_sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', 'admin')";
    if ($conn->query($insert_sql)) {
        echo "<h2 style='color: green;'>✅ Admin account created successfully!</h2>";
    } else {
        echo "<h2 style='color: red;'>❌ Failed to create admin account</h2>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Account Created</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-body p-5 text-center">
            <h1 class="mb-4">🎉 Admin Account Ready!</h1>
            
            <div class="alert alert-success">
                <h4>Admin Login Credentials:</h4>
                <p class="mb-1"><strong>Email:</strong> admin@sunray.com</p>
                <p class="mb-0"><strong>Password:</strong> password123</p>
            </div>
            
            <div class="alert alert-info">
                <h4>User Login Credentials:</h4>
                <p class="mb-1"><strong>Email:</strong> john@example.com</p>
                <p class="mb-0"><strong>Password:</strong> password123</p>
            </div>
            
            <hr class="my-4">
            
            <div class="d-grid gap-2">
                <a href="role-selection.php" class="btn btn-primary btn-lg">Go to Login Page</a>
                <a href="login.php?role=admin" class="btn btn-danger btn-lg">Login as Admin</a>
                <a href="index_updated.php" class="btn btn-secondary btn-lg">Go to Homepage</a>
            </div>
            
            <hr class="my-4">
            
            <p class="text-muted small mb-0">
                You can delete this file (create-admin.php) after creating the admin account.
            </p>
        </div>
    </div>
</body>
</html>
